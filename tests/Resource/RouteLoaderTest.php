<?php

declare(strict_types=1);

namespace Test\Resource;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Pandawa\Component\Bus\MessageRegistry;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Resource\Http\Controller\MessageController;
use Pandawa\Component\Resource\Http\Controller\ResourceController;
use Pandawa\Component\Resource\Routing\Configurator\RouteMessageConfigurator;
use Pandawa\Component\Resource\Routing\Configurator\RouteResourceConfigurator;
use Pandawa\Component\Resource\Routing\Loader\MessageLoader;
use Pandawa\Component\Resource\Routing\Loader\ResourceLoader;
use Pandawa\Component\Routing\Configurator\ChainRouteConfigurator;
use Pandawa\Component\Routing\Configurator\RouteMiddlewareConfigurator;
use Pandawa\Component\Routing\Configurator\RouteNameConfigurator;
use Pandawa\Component\Routing\Configurator\RouteOptionsConfigurator;
use Pandawa\Component\Routing\Configurator\RouteParamsConfigurator;
use Pandawa\Component\Routing\GroupRegistry;
use Pandawa\Component\Routing\Loader\ArrayLoader;
use Pandawa\Component\Routing\Loader\FileLoader;
use Pandawa\Component\Routing\Loader\GroupLoader;
use Pandawa\Component\Routing\LoaderResolver;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Routing\GroupRegistryInterface;
use Pandawa\Contracts\Routing\LoaderResolverInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Test\Resource\Command\CreatePost;
use Test\Resource\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RouteLoaderTest extends TestCase
{
    /**
     * @dataProvider provideRoutes
     */
    public function testLoadRoutes(array $routes, array $tests): void
    {
        $app = $this->createApp();
        $router = $this->createRouter($app);
        $resolver = $this->createResolver($app);

        $loader = $resolver->resolve($routes);

        $this->assertNotNull($loader);

        $loader->load($routes);

        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();

        foreach ($tests as $test) {
            $this->assertNotNull($router->getRoutes()->getByName($test['name']));

            $route = $router->getRoutes()->match(
                Request::create($test['uri'], $test['method'])
            );

            $this->assertNotNull($route);
            $this->assertSame($test['accept_methods'], $route->methods());

            foreach ($test['options'] as $key => $expect) {
                $this->assertSame($expect, $route->defaults[$key]);
            }
        }
    }

    protected function provideRoutes(): array
    {
        return [
            'Test Resource' => [
                [
                    'type'     => 'resource',
                    'resource' => Post::class,
                    'uri'      => 'posts',
                    'name'     => 'posts',
                    'options'  => [
                        'index'  => [
                            'paginate' => 10,
                        ],
                        'show'   => [
                            'rules' => ['show'],
                        ],
                        'store'  => [
                            'http_code' => 200,
                        ],
                        'update' => [
                            'http_code' => 201,
                        ],
                        'delete' => [
                            'http_code' => 400,
                        ],
                    ],
                ],
                [
                    [
                        'uri'            => 'posts',
                        'method'         => 'GET',
                        'accept_methods' => ['GET', 'HEAD'],
                        'options'        => [
                            'paginate' => 10,
                            'resource' => Post::class,
                        ],
                        'name'           => 'posts.index',
                    ],
                    [
                        'uri'            => 'posts/1',
                        'method'         => 'GET',
                        'accept_methods' => ['GET', 'HEAD'],
                        'options'        => [
                            'rules'    => ['show'],
                            'resource' => Post::class,
                        ],
                        'name'           => 'posts.show',
                    ],
                    [
                        'uri'            => 'posts',
                        'method'         => 'POST',
                        'accept_methods' => ['POST'],
                        'options'        => [
                            'http_code' => 200,
                            'resource'  => Post::class,
                        ],
                        'name'           => 'posts.store',
                    ],
                    [
                        'uri'            => 'posts/1',
                        'method'         => 'PATCH',
                        'accept_methods' => ['PATCH'],
                        'options'        => [
                            'http_code' => 201,
                            'resource'  => Post::class,
                        ],
                        'name'           => 'posts.update',
                    ],
                    [
                        'uri'            => 'posts/1',
                        'method'         => 'DELETE',
                        'accept_methods' => ['DELETE'],
                        'options'        => [
                            'http_code' => 400,
                            'resource'  => Post::class,
                        ],
                        'name'           => 'posts.delete',
                    ],
                ],
            ],
            'Test Message'  => [
                [
                    'type'    => 'message',
                    'message' => CreatePost::class,
                    'uri'     => 'posts',
                    'methods' => 'POST',
                    'name'    => 'posts.create',
                    'options' => [
                        'http_code' => 201,
                        'rules'     => ['post'],
                    ],
                ],
                [
                    [
                        'uri'            => 'posts',
                        'method'         => 'POST',
                        'accept_methods' => ['POST'],
                        'options'        => [
                            'http_code' => 201,
                            'message'   => CreatePost::class,
                            'rules'     => ['post'],
                        ],
                        'name'           => 'posts.create',
                    ]
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
        $app->bind(RegistryInterface::class, function ($app) {
            return new MessageRegistry($app, Serializer::class);
        });
        $app->bind(GroupRegistryInterface::class, GroupRegistry::class);

        $this->registerConfigurator($app);
        $this->registerLoader($app);

        return $app;
    }

    protected function registerConfigurator(Application $app): void
    {
        $app->bind(RouteConfiguratorInterface::class, fn(Container $container) => new ChainRouteConfigurator([
            $container->make(RouteMiddlewareConfigurator::class),
            $container->make(RouteNameConfigurator::class),
            $container->make(RouteParamsConfigurator::class),
            $container->make(RouteOptionsConfigurator::class),
            $container->make(RouteResourceConfigurator::class),
            $container->make(RouteMessageConfigurator::class),
        ]));
    }

    protected function registerLoader(Application $app): void
    {
        $app->bind(LoaderResolverInterface::class, fn(Container $container) => new LoaderResolver([
            $container->make(ArrayLoader::class),
            $container->make(FileLoader::class),
            $container->make(GroupLoader::class),
            $container->make(ResourceLoader::class, [
                'controller' => ResourceController::class,
            ]),
            $container->make(MessageLoader::class, [
                'controller' => MessageController::class,
            ]),
        ]));
    }
}
