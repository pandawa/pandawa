<?php

declare(strict_types=1);

namespace Pandawa\Component\Queue\Handler;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Pandawa\Component\Bus\Serializer\NativeSerializer;
use Pandawa\Component\Bus\Stamp\MessageNameStamp;
use Pandawa\Component\Queue\Stamp\QueueMiddlewaresStamp;
use Pandawa\Component\Queue\Stamp\UniqueStamp;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Envelope;
use Pandawa\Contracts\Bus\Message\Metadata;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use ReflectionClass;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CallQueuedHandler
{
    public function __construct(
        protected readonly BusInterface $messageBus,
        protected readonly RegistryInterface $registry,
        protected readonly Container $container,
    ) {
    }

    public function call(Job $job, array $data): void
    {
        try {
            $envelope = $this->setJobInstanceIfNecessary(
                $job,
                $this->getCommand($data)
            );
        } catch (ModelNotFoundException $e) {
            $this->handleModelNotFound($job, $e);

            return;
        }

        if ($this->shouldBeUnique($envelope)) {
            $this->ensureUniqueJobLockIsReleased($envelope);
        }

        $this->dispatchThroughMiddleware($job, $envelope);

        if (!$job->isReleased() && !$this->shouldBeUnique($envelope)) {
            $this->ensureUniqueJobLockIsReleased($envelope);
        }

        if (!$job->hasFailed() && !$job->isReleased()) {
            $this->ensureNextJobInChainIsDispatched($envelope);
            $this->ensureSuccessfulBatchJobIsRecorded($envelope);
        }

        if (!$job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    public function failed(array $data, $e, string $uuid): void
    {
        $envelope = $this->getCommand($data);

        if (!$this->shouldBeUnique($envelope)) {
            $this->ensureUniqueJobLockIsReleased($envelope);
        }

        $this->ensureFailedBatchJobIsRecorded($uuid, $envelope, $e);
        $this->ensureChainCatchCallbacksAreInvoked($uuid, $envelope, $e);

        if (method_exists($envelope->message, 'failed')) {
            $envelope->message->failed($e);
        }
    }

    protected function ensureChainCatchCallbacksAreInvoked(string $uuid, Envelope $envelope, $e): void
    {
        if (method_exists($envelope->message, 'invokeChainCatchCallbacks')) {
            $envelope->message->invokeChainCatchCallbacks($e);
        }
    }

    protected function ensureFailedBatchJobIsRecorded(string $uuid, Envelope $envelope, $e): void
    {
        if (! in_array(Batchable::class, class_uses_recursive($envelope->message))) {
            return;
        }

        if ($batch = $envelope->message->batch()) {
            $batch->recordFailedJob($uuid, $e);
        }
    }

    protected function ensureSuccessfulBatchJobIsRecorded(Envelope $envelope): void
    {
        $uses = class_uses_recursive($envelope->message);

        if (! in_array(Batchable::class, $uses) ||
            ! in_array(InteractsWithQueue::class, $uses)) {
            return;
        }

        if ($batch = $envelope->message->batch()) {
            $batch->recordSuccessfulJob($envelope->message->job->uuid());
        }
    }

    protected function ensureNextJobInChainIsDispatched(Envelope $envelope): void
    {
        if (method_exists($envelope->message, 'dispatchNextJobInChain')) {
            $envelope->message->dispatchNextJobInChain();
        }
    }

    protected function dispatchThroughMiddleware(Job $job, Envelope $envelope): mixed
    {
        return (new Pipeline($this->container))
            ->send($envelope)
            ->through([
                ...(method_exists($envelope->message, 'middleware') ? $envelope->message->middleware() : []),
                ...($envelope->message->middleware ?? []),
                ...($envelope->last(QueueMiddlewaresStamp::class)?->middlewares ?? []),
            ])
            ->then(function ($command) use ($job) {
                return $this->messageBus->dispatchNow(
                    $command,
                    $this->resolveHandler($job, $command)
                );
            });
    }

    protected function resolveHandler(Job $job, object $command): ?object
    {
        $handler = $this->messageBus->getCommandHandler($command) ?: null;

        if ($handler) {
            $this->setJobInstanceIfNecessary($job, $handler);
        }

        return $handler;
    }

    protected function ensureUniqueJobLockIsReleased(Envelope $envelope): void
    {
        if (!$envelope->message instanceof ShouldBeUnique) {
            return;
        }

        $uniqueId = $envelope->last(UniqueStamp::class)?->uniqueId
            ?? (
            method_exists($envelope->message, 'uniqueId')
                ? $envelope->message->uniqueId()
                : ($envelope->message->uniqueId ?? '')
            );

        $cache = method_exists($envelope->message, 'uniqueVia')
            ? $envelope->message->uniqueVia()
            : $this->container->make(Cache::class);

        $name = $envelope->last(MessageNameStamp::class)?->name ?? get_class($envelope->message);

        $cache->lock('laravel_unique_job:'.$name.$uniqueId)->forceRelease();
    }

    protected function getCommand(array $data): Envelope
    {
        if (true === ($data['encrypted'] ?? false) && $this->container->bound(Encrypter::class)) {
            $message = unserialize($this->container[Encrypter::class]->decrypt($data['command']));
        } else {
            $message = $this->denormalize($data);
        }

        return $this->messageBus->wrap($message);
    }

    protected function denormalize(array $data): mixed
    {
        $metadata = $this->getMetadata($data);
        $serializer = $this->makeSerializer($metadata);

        if ($metadata->class === CallQueuedListener::class) {
            return new CallQueuedListener(
                $data['command']['class'],
                $data['command']['method'],
                array_map(
                    function (mixed $value) use ($serializer) {
                        if (is_array($value) && $value['__normalized_class'] ?? null) {
                            return $serializer->denormalize($value['payload'], $value['__normalized_class']);
                        }

                        return $value;
                    },
                    $data['command']['data'] ?? []
                )
            );
        }

        return $serializer->denormalize($data['command'], $metadata->class);
    }

    /**
     * @template TCommand
     *
     * @param  Job  $job
     * @param  TCommand  $command
     *
     * @return TCommand
     */
    protected function setJobInstanceIfNecessary(Job $job, object $command): object
    {
        $message = $command instanceof Envelope ? $command->message : $command;

        if (in_array(InteractsWithQueue::class, class_uses_recursive($message))) {
            $message->setJob($job);
        }

        return $command;
    }

    protected function handleModelNotFound(Job $job, $e): void
    {
        $class = $job->resolveName();

        try {
            $shouldDelete = (new ReflectionClass($class))
                                ->getDefaultProperties()['deleteWhenMissingModels'] ?? false;
        } catch (Exception $e) {
            $shouldDelete = false;
        }

        if ($shouldDelete) {
            $job->delete();

            return;
        }

        $job->fail($e);
    }

    protected function getMetadata(array $data): Metadata
    {
        if ($this->registry->hasName($data['commandName'])) {
            return $this->registry->getByName($data['commandName']);
        }

        if ($this->registry->has($data['commandClass'])) {
            return $this->registry->get($data['commandClass']);
        }

        return new Metadata($data['commandClass'], serializer: NativeSerializer::class);
    }

    protected function makeSerializer(Metadata $metadata): SerializerInterface|NormalizerInterface
    {
        return $this->container->make($metadata->serializer);
    }

    protected function shouldBeUnique(Envelope $envelope): bool
    {
        return $envelope->last(UniqueStamp::class)
            || $envelope->message instanceof ShouldBeUniqueUntilProcessing;
    }
}
