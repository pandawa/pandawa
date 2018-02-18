<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Illuminate\Routing\Router;
use Pandawa\Module\Api\Routing\RouteLoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RouteProviderTrait
{
    public function bootRouteProvider(): void
    {
        if ($this->app->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRoutes();

            $this->app->booted(
                function () {
                    $this->app['router']->getRoutes()->refreshNameLookups();
                    $this->app['router']->getRoutes()->refreshActionLookups();
                }
            );
        }
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(
            [$this->app->make(Router::class), $method],
            $parameters
        );
    }

    /**
     * Load the cached routes for the application.
     */
    protected function loadCachedRoutes(): void
    {
        $this->app->booted(
            function () {
                require $this->app->getCachedRoutesPath();
            }
        );
    }

    /**
     * Load the application routes.
     */
    protected function loadRoutes(): void
    {
        $this->loader()->load($this->routes());
    }

    protected function loader(): RouteLoaderInterface
    {
        return $this->app[RouteLoaderInterface::class];
    }

    abstract protected function routes(): array;
}
