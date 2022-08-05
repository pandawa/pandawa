<?php

declare(strict_types=1);

namespace Pandawa\Contracts\DependencyInjection\Factory;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ServiceFactoryInterface
{
    public function create(array $config): callable|string;

    public function supports(array $config): bool;
}
