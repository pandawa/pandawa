<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Factory;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Container\Container;
use Pandawa\Contracts\Eloquent\Cache\CacheHandlerInterface;
use Pandawa\Contracts\Eloquent\Factory\CacheHandlerFactoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CacheHandlerFactory implements CacheHandlerFactoryInterface
{
    public function __construct(
        protected readonly Container $container,
        protected readonly array $config,
    ) {
    }

    public function create(): ?CacheHandlerInterface
    {
        if (true !== $this->config['enabled']) {
            return null;
        }

        $cacheHandlerClass = $this->config['handler'];

        return new $cacheHandlerClass(
            $this->cacheFactory()->store($this->config['store']),
            $this->config['ttl']
        );
    }

    protected function cacheFactory(): CacheFactory
    {
        return $this->container->get('cache');
    }
}
