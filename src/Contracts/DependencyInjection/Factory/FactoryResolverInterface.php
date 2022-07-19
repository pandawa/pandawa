<?php

declare(strict_types=1);

namespace Pandawa\Contracts\DependencyInjection\Factory;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface FactoryResolverInterface
{
    public function resolve(array $config): ?ServiceFactoryInterface;
}
