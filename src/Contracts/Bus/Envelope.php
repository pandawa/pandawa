<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Envelope
{
    /**
     * @var array<class-string<StampInterface>, list<StampInterface>>
     */
    private readonly array $stamps;

    public function __construct(public readonly object $message, array $stamps = [])
    {
        $normalizedStamps = [];

        foreach ($stamps as $class => $stamp) {
            if (is_string($class)) {
                $normalizedStamps[$class] = $stamp;

                continue;
            }

            $normalizedStamps[get_class($stamp)][] = $stamp;
        }

        $this->stamps = $normalizedStamps;
    }

    /**
     * Write message with envelope.
     *
     * @param  object  $message
     * @param  array  $stamps
     *
     * @return static
     */
    public static function wrap(object $message, array $stamps = []): self
    {
        $envelope = $message instanceof self ? $message : new self($message);

        return $envelope->with(...$stamps);
    }

    /**
     * Add stamps to envelope.
     */
    public function with(StampInterface ...$stamps): self
    {
        return new self($this->message, [...$this->all(), ...$stamps]);
    }

    /**
     * Remove all stamps of given class.
     */
    public function without(string $stampClass): self
    {
        $newStamps = $this->stamps;

        unset($newStamps[$stampClass]);

        return new self($this->message, $newStamps);
    }

    /**
     * Remove all stamps that implement given type.
     */
    public function withoutTypeOf(string $type): self
    {
        $newStamps = $this->stamps;

        foreach (array_keys($newStamps) as $class) {
            if ($class === $type || is_subclass_of($class, $type)) {
                unset($newStamps[$class]);
            }
        }

        return new self($this->message, $newStamps);
    }

    /**
     * Get last stamp of the given class.
     *
     * @template TStamp of StampInterface
     *
     * @param  class-string<TStamp>  $stampClass
     *
     * @return TStamp|StampInterface
     */
    public function last(string $stampClass): ?StampInterface
    {
        $stamps = $this->stamps[$stampClass] ?? null;

        return $stamps ? end($stamps) : null;
    }

    /**
     * Get all stamp with filterable of the given class.
     *
     * @template TStamp of StampInterface
     *
     * @param  class-string<TStamp>|null  $stampClass
     *
     * @return array<int, TStamp>|array<string, list<StampInterface>>
     */
    public function all(?string $stampClass = null): array
    {
        if (null !== $stampClass) {
            return $this->stamps[$stampClass] ?? [];
        }

        return $this->stamps;
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->message->{$method}(...$arguments);
    }

    public function __get(string $property): mixed
    {
        return $this->message->{$property};
    }

    public function __set(string $property, mixed $value): void
    {
        $this->message->{$property} = $value;
    }
}
