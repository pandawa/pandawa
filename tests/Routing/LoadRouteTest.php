<?php

declare(strict_types=1);

namespace Test\Routing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Routing\Configurator\ChainRouteConfigurator;
use Pandawa\Component\Routing\Configurator\RouteMiddlewareConfigurator;
use Pandawa\Component\Routing\Configurator\RouteNameConfigurator;
use Pandawa\Component\Routing\Configurator\RouteParamsConfigurator;
use Pandawa\Component\Routing\GroupRegistry;
use Pandawa\Component\Routing\Loader\ArrayLoader;
use Pandawa\Component\Routing\Loader\FileLoader;
use Pandawa\Component\Routing\Loader\GroupLoader;
use Pandawa\Component\Routing\Loader\TypeLoader;
use Pandawa\Component\Routing\LoaderResolver;
use Pandawa\Contracts\Routing\GroupRegistryInterface;
use Pandawa\Contracts\Routing\LoaderResolverInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;
use PHPUnit\Framework\TestCase;
use Test\Routing\Controller\MyController;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class LoadRouteTest extends TestCase
{
    /**
     * @dataProvider routesProvider
     */
    public function testRoutes(array $routeConfig): void
    {
        $app = $this->createApp();
        $router = $this->createRouter($app);
        $resolver = $this->createResolver($app);

        $resolver->resolve($routeConfig)?->load($routeConfig);

        $route = $router->getRoutes()->match(Request::create($routeConfig['uri'], strtoupper($routeConfig['type'])));

        $this->assertNotNull($route);
        $this->assertTrue(in_array(strtoupper($routeConfig['type']), $route->methods));
        $this->assertSame($routeConfig['uri'], $route->uri);
        $this->assertSame($routeConfig['controller'], $route->getAction('controller'));

        if ($middleware = $routeConfig['middleware'] ?? null) {
            $this->assertTrue(in_array($middleware, $route->getAction('middleware')));
        }
    }

    public function routesProvider(): array
    {
        return [
            'Test GET'        => [
                [
                    'controller' => MyController::class,
                    'uri'        => '/',
                    'type'       => 'get',
                ],
            ],
            'Test OPTIONS'    => [
                [
                    'controller' => MyController::class,
                    'uri'        => '/',
                    'type'       => 'options',
                ],
            ],
            'Test POST'       => [
                [
                    'controller' => MyController::class,
                    'uri'        => 'posts',
                    'type'       => 'post',
                ],
            ],
            'Test DELETE'     => [
                [
                    'controller' => MyController::class,
                    'uri'        => 'post',
                    'type'       => 'delete',
                ],
            ],
            'Test PUT'        => [
                [
                    'controller' => MyController::class,
                    'uri'        => 'post',
                    'type'       => 'put',
                ],
            ],
            'Test PATCH'      => [
                [
                    'controller' => MyController::class,
                    'uri'        => 'post',
                    'type'       => 'patch',
                ],
            ],
            'Test MIDDLEWARE' => [
                [
                    'controller' => MyController::class,
                    'uri'        => 'post',
                    'type'       => 'patch',
                    'middleware' => 'auth',
                ],
            ],
        ];
    }

    protected function createRouter(Application $app): Router
    {
        return $app->make(Registrar::class);
    }

    protected function createResolver(Application $app): LoaderResolverInterface
    {
        return $app->make(LoaderResolverInterface::class);
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->bind(Registrar::class, Router::class);
        $app->bind(GroupRegistryInterface::class, GroupRegistry::class);

        $this->registerConfigurator($app);
        $this->registerLoader($app);

        return $app;
    }

    protected function registerConfigurator(Application $app): void
    {
        $app->bind(RouteConfiguratorInterface::class, fn(Container $container) => new ChainRouteConfigurator([
            $app->make(RouteMiddlewareConfigurator::class),
            $app->make(RouteNameConfigurator::class),
            $app->make(RouteParamsConfigurator::class),
        ]));
    }

    protected function registerLoader(Application $app): void
    {
        $app->bind(LoaderResolverInterface::class, fn(Container $container) => new LoaderResolver([
            $app->make(ArrayLoader::class),
            $app->make(TypeLoader::class),
            $app->make(FileLoader::class),
            $app->make(GroupLoader::class),
        ]));
    }
}
