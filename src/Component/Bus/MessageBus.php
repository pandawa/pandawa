<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus;

use Illuminate\Bus\Batch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Collection;
use Pandawa\Component\Bus\Stamp\MessageIdentifiedStamp;
use Pandawa\Component\Bus\Stamp\MessageNameStamp;
use Pandawa\Component\Bus\Stamp\QueuedStamp;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Envelope;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Bus\QueueFactoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageBus implements BusInterface
{
    protected Pipeline $pipeline;

    public function __construct(
        protected readonly Container $container,
        protected readonly RegistryInterface $messageRegistry,
        protected readonly QueueFactoryInterface $queueFactory,
        protected array $middlewares = [],
        protected array $handlers = [],
    ) {
        $this->pipeline = new Pipeline($this->container);
    }

    public function dispatch($command): mixed
    {
        return $this->pipeline->send($this->wrap($command))
            ->through($this->middlewares)
            ->then(function (Envelope $envelope) {
                if ($this->queueFactory->supports() && $this->messageShouldBeQueue($envelope)) {
                    return $this->dispatchToQueue($envelope);
                }

                return $this->dispatchNow($envelope);
            });
    }

    public function dispatchSync($command, $handler = null)
    {
        $envelope = $this->wrap($command);

        if ($this->queueFactory->supports() && $this->messageShouldBeQueue($envelope)) {
            if ($queuedStamp = $envelope->last(QueuedStamp::class)) {
                $queuedStamp = clone $queuedStamp;
                $queuedStamp->onConnection('sync');

                return $this->dispatchToQueue($envelope->with($queuedStamp));
            }
        }

        return $this->dispatchNow($envelope, $handler);
    }

    public function dispatchNow($command, $handler = null)
    {
        if ($this->queueFactory->supports()) {
            $uses = class_uses_recursive($command);

            if (in_array(InteractsWithQueue::class, $uses) &&
                in_array(Queueable::class, $uses) &&
                ! $command->job) {
                $command->setJob(new SyncJob($this->container, json_encode([]), 'sync', 'sync'));
            }
        }

        return $this->pipeline->send($this->wrap($command))
            ->through($this->middlewares)
            ->then(function (Envelope $envelope) use ($handler) {
                if ($handler || $handler = $this->getCommandHandler($envelope)) {
                    return $handler->{$this->getHandlerMethod($handler)}($envelope->message);
                }

                return $this->container->call([$envelope->message, $this->getHandlerMethod($envelope->message)]);
            });
    }

    public function dispatchToQueue($command)
    {
        $envelope = $this->wrap($command);
        $connection = $envelope->last(QueuedStamp::class)?->connection
            ?? ($envelope->message?->connection ?? null);

        $queue = $this->queueFactory->create($connection);

        if (method_exists($envelope->message, 'queue')) {
            return $envelope->message->queue($queue, $envelope);
        }

        return $this->pushMessageToQueue($queue, $envelope);
    }

    public function findBatch(string $batchId): ?Batch
    {
        return $this->container->make(BatchRepository::class)->find($batchId);
    }

    public function batch($jobs): PendingBatch
    {
        return new PendingBatch($this->container, Collection::wrap($jobs));
    }

    public function hasCommandHandler($command): bool
    {
        return array_key_exists($this->getMessageName($command), $this->handlers);
    }

    public function getCommandHandler($command): ?object
    {
        if ($this->hasCommandHandler($command)) {
            $handler = $this->handlers[$this->getMessageName($command)];

            if (is_object($handler)) {
                return $handler;
            }

            return $this->container->make($handler);
        }

        return null;
    }

    public function pipeThrough(array $pipes): static
    {
        $this->middlewares = $pipes;

        return $this;
    }

    public function mergePipes(array $pipes): static
    {
        $this->middlewares = [...$this->middlewares, ...$pipes];

        return $this;
    }

    public function map(array $map): static
    {
        $this->handlers = [...$this->handlers, ...$map];

        return $this;
    }

    protected function wrap(object $message): Envelope
    {
        $envelope = Envelope::wrap($message);

        if ($envelope->last(MessageIdentifiedStamp::class)) {
            return $envelope;
        }

        $envelope = $envelope->with(new MessageIdentifiedStamp());

        if ($this->messageRegistry->has($messageClass = get_class($envelope->message))) {
            $metadata = $this->messageRegistry->get($messageClass);

            if (!$envelope->last(MessageNameStamp::class) && $metadata->name) {
                $envelope = $envelope->with(new MessageNameStamp($metadata->name));
            }

            if (count($metadata->stamps)) {
                $envelope = $envelope->with(...$metadata->stamps);
            }
        }

        return $envelope;
    }

    protected function pushMessageToQueue(Queue $queue, object $message): mixed
    {
        $envelope = $this->wrap($message);
        $stamp = $envelope->last(QueuedStamp::class);
        $delay = $stamp?->delay ?? ($envelope->message?->delay ?? null);
        $channel = $stamp?->queue ?? ($envelope->message?->queue ?? null);

        if (null !== $channel && null !== $delay) {
            return $queue->laterOn($channel, $delay, $envelope);
        }

        if (null !== $channel) {
            return $queue->pushOn($channel, $envelope);
        }

        if (null !== $delay) {
            return $queue->later($delay, $envelope);
        }

        return $queue->push($envelope);
    }

    protected function messageShouldBeQueue($message): bool
    {
        $envelope = $this->wrap($message);

        return null !== $envelope->last(QueuedStamp::class) || $envelope->message instanceof ShouldQueue;
    }

    protected function getMessageName(object $message): string
    {
        $envelope = $this->wrap($message);

        return $envelope->last(MessageNameStamp::class)?->name ?? get_class($envelope->message);
    }

    protected function getHandlerMethod(object $handler): string
    {
        return method_exists($handler, 'handle') ? 'handle' : '__invoke';
    }
}
