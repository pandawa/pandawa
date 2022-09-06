<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Routing;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface GroupRegistryInterface
{
    public function add(string $group, array $options): void;

    public function has(string $group): bool;

    public function get(string $group): array;
}
