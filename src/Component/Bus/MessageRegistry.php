<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus;

use Illuminate\Contracts\Container\Container;
use Pandawa\Component\Bus\Exception\MessageNotFoundException;
use Pandawa\Contracts\Bus\Message\Metadata;
use Pandawa\Contracts\Bus\Message\RegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageRegistry implements RegistryInterface
{
    /**
     * @var array<string, array{name:string|null, stamps:array<int, array<int,array{class: string, arguments: string[]|int[]|bool[]}>>}>
     */
    protected array $messages = [];
    protected array $names = [];

    public function __construct(
        protected readonly Container $container,
        array $messages = [],
    ) {
        $this->load($messages);
    }

    public function load(array $messages): void
    {
        foreach ($messages as $class => $message) {
            $this->add($class, $message);
        }
    }

    /**
     * @param array{name:string|null, stamps:array<int, array<int,array{class: string, arguments: string[]|int[]|bool[]}>>} $message
     */
    public function add(string $class, array $message): void
    {
        $this->messages[$class] = $message;

        if (null !== $name = $message['name'] ?? null) {
            $this->names[$name] = $class;
        }
    }

    public function has(string $class): bool
    {
        return array_key_exists($class, $this->messages);
    }

    public function get(string $class): Metadata
    {
        if ($this->has($class)) {
            return $this->makeMetadata($class, $this->messages[$class]);
        }

        throw new MessageNotFoundException(sprintf(
            'Message with class "%s" is not found.',
            $class
        ));
    }

    public function hasName(string $name): bool
    {
        return array_key_exists($name, $this->names);
    }

    public function getByName(string $name): Metadata
    {
        if ($this->hasName($name)) {
            $class = $this->names[$name];
            $message = $this->messages[$class];

            return $this->makeMetadata($class, $message);
        }

        throw new MessageNotFoundException(sprintf(
            'Message with name "%s" is not found.',
            $name
        ));
    }

    protected function makeMetadata(string $class, $message): Metadata
    {
        return new Metadata(
            class: $class,
            name: $message['name'] ?? null,
            stamps: array_map(
                function (array $stamp) {
                    return $this->container->make($stamp['class'], $stamp['arguments'] ?? []);
                },
                $message['stamps'] ?? []
            )
        );
    }
}
