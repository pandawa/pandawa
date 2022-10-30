<?php

declare(strict_types=1);

namespace Pandawa\Component\Queue;

use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\Queue as LaravelQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pandawa\Component\Bus\Stamp\MessageNameStamp;
use Pandawa\Component\Bus\Stamp\SerializerStamp;
use Pandawa\Component\Queue\Handler\CallQueuedListener;
use Pandawa\Component\Queue\Stamp\AfterCommitStamp;
use Pandawa\Component\Queue\Stamp\BackoffStamp;
use Pandawa\Component\Queue\Stamp\EncryptStamp;
use Pandawa\Component\Queue\Stamp\FailOnTimeoutStamp;
use Pandawa\Component\Queue\Stamp\MaxExceptionsStamp;
use Pandawa\Component\Queue\Stamp\RetryUntilStamp;
use Pandawa\Component\Queue\Stamp\TimeoutStamp;
use Pandawa\Component\Queue\Stamp\TriesStamp;
use Pandawa\Contracts\Bus\Envelope;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @mixin LaravelQueue
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait Queue
{
    protected function createPayload($job, $queue, $data = ''): string
    {
        if (is_object($job)) {
            $job = Envelope::wrap($job);

            if ($job->message instanceof Closure) {
                $job = Envelope::wrap(CallQueuedClosure::create($job->message), $job->all());
            }
        }

        return json_encode($this->createPayloadArray($job, $queue, $data), JSON_UNESCAPED_UNICODE);
    }

    protected function createObjectPayload($job, $queue): array
    {
        $envelope = Envelope::wrap($job);
        $payload = $this->withCreatePayloadHooks($queue, [
            'uuid'          => (string) Str::uuid(),
            'displayName'   => $this->getDisplayName($envelope),
            'job'           => 'Pandawa\Component\Queue\Handler\CallQueuedHandler@call',
            'maxTries'      => $this->getMaxTries($envelope),
            'maxExceptions' => $this->getMaxExceptions($envelope),
            'failOnTimeout' => $this->isFailOnTimeout($envelope),
            'backoff'       => $this->getJobBackoff($envelope),
            'timeout'       => $this->getTimeout($envelope),
            'retryUntil'    => $this->getJobExpiration($envelope),
        ]);

        $encrypted = $this->jobShouldBeEncrypted($envelope) && $this->container->bound(Encrypter::class);
        $message = $encrypted
            ? $this->container[Encrypter::class]->encrypt(serialize($envelope->message))
            : $this->normalize($envelope);

        return [
            ...$payload,
            'data' => [
                'commandName'  => $envelope->last(MessageNameStamp::class)?->name ?? get_class($envelope->message),
                'commandClass' => get_class($envelope->message),
                'command'      => $message,
                'encrypted'    => $encrypted,
            ],
        ];
    }

    protected function normalize(Envelope $envelope): array
    {
        try {
            return [
                'type'       => 'serializer',
                'serialized' => $this->normalizeFromSerializer($envelope),
            ];
        } catch (\Exception $e) {
            return [
                'type'       => 'native',
                'serialized' => serialize($envelope->message),
            ];
        }
    }

    protected function normalizeFromSerializer(Envelope $envelope): array
    {
        $serializer = $this->makeSerializer($envelope);

        if ($envelope->message instanceof CallQueuedListener) {
            return [
                'class'  => $envelope->message->class,
                'method' => $envelope->message->method,
                'data'   => array_map(
                    function (mixed $value) use ($envelope, $serializer) {
                        if ($value instanceof Envelope) {
                            return [
                                '__normalized_class' => get_class($value->message),
                                'payload'            => $this->normalizeFromSerializer($value),
                            ];
                        }

                        if (is_object($value)) {
                            return [
                                '__normalized_class' => get_class($value),
                                'payload'            => $serializer->normalize($value),
                            ];
                        }

                        return $value;
                    },
                    $envelope->message->data
                ),
            ];
        }

        return $serializer->normalize($envelope->message);
    }

    protected function createStringPayload($job, $queue, $data): array
    {
        return $this->withCreatePayloadHooks($queue, [
            'uuid'          => (string) Str::uuid(),
            'displayName'   => is_string($job) ? explode('@', $job)[0] : null,
            'job'           => $job,
            'maxTries'      => null,
            'maxExceptions' => null,
            'failOnTimeout' => false,
            'backoff'       => null,
            'timeout'       => null,
            'data'          => $data,
        ]);
    }

    protected function getDisplayName($job): string
    {
        $envelope = Envelope::wrap($job);

        if ($stamp = $envelope->last(MessageNameStamp::class)) {
            return $stamp->name;
        }

        return method_exists($envelope->message, 'displayName')
            ? $envelope->message->displayName()
            : get_class($envelope->message);
    }

    protected function jobShouldBeEncrypted($job): bool
    {
        $envelope = Envelope::wrap($job);

        if ($envelope->last(EncryptStamp::class)) {
            return true;
        }

        if ($envelope->message instanceof ShouldBeEncrypted) {
            return true;
        }

        return isset($envelope->message->shouldBeEncrypted) && $envelope->message->shouldBeEncrypted;
    }

    public function getJobBackoff($job): ?string
    {
        $envelope = Envelope::wrap($job);
        $backoff = null;

        if (null !== $stamp = $envelope->last(BackoffStamp::class)) {
            $backoff = $stamp->backoff;
        } elseif (isset($envelope->message->backoff)) {
            $backoff = $envelope->message->backoff;
        } elseif (method_exists($envelope->message, 'backoff')) {
            $backoff = $envelope->message->backoff();
        }

        if (null === $backoff) {
            return null;
        }

        return collect(Arr::wrap($backoff))
            ->map(function ($backoff) {
                return $backoff instanceof DateTimeInterface
                    ? $this->secondsUntil($backoff)
                    : $backoff;
            })
            ->implode(';');
    }

    public function getJobExpiration($job): ?int
    {
        $envelope = Envelope::wrap($job);

        if (null !== $stamp = $envelope->last(RetryUntilStamp::class)) {
            return $stamp->expiration;
        }

        if (!method_exists($envelope->message, 'retryUntil') && !isset($envelope->message->retryUntil)) {
            return null;
        }

        $expiration = $envelope->message->retryUntil ?? $envelope->message->retryUntil();

        return $expiration instanceof DateTimeInterface
            ? $expiration->getTimestamp()
            : $expiration;
    }

    protected function shouldDispatchAfterCommit($job): bool
    {
        if (!is_object($job)) {
            return false;
        }

        $job = Envelope::wrap($job);

        if (null !== $job->last(AfterCommitStamp::class)) {
            return true;
        }

        if (isset($job->message->afterCommit)) {
            return $job->message->afterCommit;
        }

        if (isset($this->dispatchAfterCommit)) {
            return $this->dispatchAfterCommit;
        }

        return false;
    }

    protected function createPayloadArray($job, $queue, $data = ''): array
    {
        $payload = is_object($job)
            ? $this->createObjectPayload($job, $queue)
            : $this->createStringPayload($job, $queue, $data);

        return [
            ...$payload,
            ...$this->additionalPayload(),
        ];
    }

    protected function additionalPayload(): array
    {
        return [];
    }

    protected function getMaxTries(Envelope $envelope): ?int
    {
        return $envelope->last(TriesStamp::class)?->tries ?? ($envelope->message->tries ?? null);
    }

    protected function getMaxExceptions(Envelope $envelope): ?int
    {
        return $envelope->last(MaxExceptionsStamp::class)->maxExceptions ?? ($envelope->message->maxExceptions ?? null);
    }

    protected function getTimeout(Envelope $envelope): ?int
    {
        return $envelope->last(TimeoutStamp::class)->timeout ?? ($envelope->message->timeout ?? null);
    }

    protected function isFailOnTimeout(Envelope $envelope): bool
    {
        return null !== $envelope->last(FailOnTimeoutStamp::class)
            ? true
            : ($envelope->message->failOnTimeout ?? false);
    }

    protected function makeSerializer(Envelope $envelope): SerializerInterface
    {
        return $this->container->make($this->getSerializer($envelope));
    }

    protected function getSerializer(Envelope $envelope): string
    {
        return $envelope->last(SerializerStamp::class)->serializer ?? Serializer::class;
    }
}
