<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface FactoryResolverInterface
{
    public function resolve(string $model): ?FactoryInterface;
}
