<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Persistent\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Pandawa\Component\Eloquent\Model;
use Pandawa\Contracts\Eloquent\Action\Action;
use Pandawa\Contracts\Eloquent\Cache\CacheHandlerInterface;
use Pandawa\Contracts\Eloquent\Factory\CacheHandlerFactoryInterface;
use Pandawa\Contracts\Eloquent\Persistent\MiddlewareInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class InvalidateCache implements MiddlewareInterface
{
    public function __construct(protected Container $container)
    {
    }

    public function handle(Action $action, Closure $next): mixed
    {
        return tap($next($action), function (Model $model) {
            $this->getCacheHandler()?->invalidate($model);
        });
    }

    protected function getCacheHandler(): ?CacheHandlerInterface
    {
        if ($this->container->has(CacheHandlerFactoryInterface::class)) {
            return $this->container->get(CacheHandlerFactoryInterface::class)->create();
        }

        return null;
    }
}
