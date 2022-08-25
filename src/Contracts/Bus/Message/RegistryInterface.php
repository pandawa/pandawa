<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus\Message;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RegistryInterface
{
    public function load(array $messages): void;

    public function add(string $class, array $message): void;

    public function has(string $class): bool;

    public function get(string $class): Metadata;

    public function hasName(string $name): bool;

    public function getByName(string $name): Metadata;
}
