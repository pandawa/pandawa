<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Persistent\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Pandawa\Contracts\Eloquent\Action\Action;
use Pandawa\Contracts\Eloquent\Persistent\MiddlewareInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabaseTransaction implements MiddlewareInterface
{
    public function __construct(protected readonly Container $container)
    {
    }

    public function handle(Action $action, Closure $next): mixed
    {
        return $this->getConnection()->transaction(fn() => $next($action));
    }

    protected function getConnection(): ConnectionInterface
    {
        return $this->container->get('db')->connection();
    }
}
