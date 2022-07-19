<?php

declare(strict_types=1);

namespace Pandawa\Contracts\DependencyInjection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ServiceRegistryInterface
{
    public function load(array $services): void;

    public function register(string $name, array $config): void;
}
