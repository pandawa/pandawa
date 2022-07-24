<?php

declare(strict_types=1);

namespace Pandawa\Contracts\DependencyInjection\Factory;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ServiceFactoryInterface
{
    public function create(array $config): callable;

    public function supports(array $config): bool;
}
