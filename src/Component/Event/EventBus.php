<?php

declare(strict_types=1);

namespace Pandawa\Component\Event;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Events\Dispatcher;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Pandawa\Component\Bus\Factory\EnvelopeFactory;
use Pandawa\Component\Bus\Stamp\MessageNameStamp;
use Pandawa\Component\Queue\Handler\CallQueuedListener;
use Pandawa\Contracts\Bus\Envelope;
use Pandawa\Contracts\Event\EventBusInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EventBus extends Dispatcher implements EventBusInterface
{
    protected array $middlewares = [];
    protected readonly Pipeline $pipeline;

    public function __construct(
        protected readonly EnvelopeFactory $envelopeFactory,
        ?Container $container = null,
    ) {
        parent::__construct($container);

        $this->pipeline = new Pipeline($this->container);
    }

    public function mergeMiddlewares(array $middlewares): void
    {
        $this->middlewares = [...$this->middlewares, ...$middlewares];
    }

    public function fire(object|string $event, mixed $payload = []): mixed
    {
        return $this->dispatch($event, $payload);
    }

    public function dispatch($event, $payload = [], $halt = false): mixed
    {
        return $this->pipeline
            ->send($this->wrap($event, $payload))
            ->through($this->middlewares)
            ->then(fn (Envelope $envelope) => parent::dispatch($envelope, $payload, $halt));
    }

    public function makeListener($listener, $wildcard = false): callable
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener, $wildcard);
        }

        if (is_array($listener) && isset($listener[0]) && is_string($listener[0])) {
            return $this->createClassListener($listener, $wildcard);
        }

        return $this->createCallableListener(
            function ($event, $payload) use ($listener, $wildcard) {
                if ($wildcard) {
                    return $listener($event, $payload);
                }

                return $listener(...array_values($payload));
            }
        );
    }

    protected function createClassCallable($listener): callable
    {
        [$class, $method] = is_array($listener)
            ? $listener
            : $this->parseClassCallable($listener);

        if (! method_exists($class, $method)) {
            $method = '__invoke';
        }

        if ($this->handlerShouldBeQueued($class)) {
            return $this->createQueuedHandlerCallable($class, $method);
        }

        $listener = $this->container->make($class);

        $callable = $this->handlerShouldBeDispatchedAfterDatabaseTransactions($listener)
            ? $this->createCallbackForListenerRunningAfterCommits($listener, $method)
            : [$listener, $method];

        return $this->createCallableListener($callable);
    }

    protected function createListenerAndJob($class, $method, $arguments): array
    {
        $listener = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        return [$listener, $this->propagateListenerOptions(
            $listener, new CallQueuedListener($class, $method, $arguments)
        )];
    }

    /**
     * @param Envelope $event
     * @param mixed $payload
     *
     * @return array
     */
    protected function parseEventAndPayload($event, $payload): array
    {
        $eventName = is_object($event?->message) ? $this->getEventName($event) : $event;

        if ($event->message instanceof NoneObjectEvent) {
            $payload = Arr::wrap($payload);
        } else {
            $payload = [$event];
        }

        return [$eventName, $payload];
    }

    protected function createCallableListener(callable $callback): callable
    {
        return function () use ($callback) {
            $payload = $this->normalizePayload(func_get_args());

            return $callback(...$payload);
        };
    }

    protected function propagateListenerOptions($listener, $job): mixed
    {
        return tap($job, function ($job) use ($listener) {
            $data = $this->normalizePayload(array_values($job->data));

            $job->afterCommit = property_exists($listener, 'afterCommit') ? $listener->afterCommit : null;
            $job->backoff = method_exists($listener, 'backoff') ? $listener->backoff(...$data) : ($listener->backoff ?? null);
            $job->maxExceptions = $listener->maxExceptions ?? null;
            $job->retryUntil = method_exists($listener, 'retryUntil') ? $listener->retryUntil(...$data) : null;
            $job->shouldBeEncrypted = $listener instanceof ShouldBeEncrypted;
            $job->timeout = $listener->timeout ?? null;
            $job->tries = $listener->tries ?? null;

            $job->through(array_merge(
                method_exists($listener, 'middleware') ? $listener->middleware(...$data) : [],
                $listener->middleware ?? []
            ));
        });
    }

    protected function normalizePayload(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(
                fn(mixed $value) => $this->normalizePayload($value),
                $value
            );
        }

        if ($value instanceof Envelope) {
            return $value->message;
        }

        return $value;
    }

    protected function getEventName(Envelope $envelope): string
    {
        if ($envelope->message instanceof NoneObjectEvent) {
            return $envelope->message->event;
        }

        return $envelope->last(MessageNameStamp::class)?->name ?? get_class($envelope->message);
    }

    protected function wrap(mixed $event, array $attributes = []): object
    {
        if (!is_object($event)) {
            return $this->envelopeFactory->wrapByName($event, $attributes);
        }

        return $this->envelopeFactory->wrap($event);
    }
}
