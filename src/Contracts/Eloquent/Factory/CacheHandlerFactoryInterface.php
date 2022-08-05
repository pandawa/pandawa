<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Factory;

use Pandawa\Contracts\Eloquent\Cache\CacheHandlerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface CacheHandlerFactoryInterface
{
    public function create(): ?CacheHandlerInterface;
}
