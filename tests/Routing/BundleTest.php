<?php

declare(strict_types=1);

namespace Test\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\RoutingBundle\RoutingBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Contracts\Routing\LoaderResolverInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;
use PHPUnit\Framework\TestCase;
use Test\Routing\Controller\MyController;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    /**
     * @dataProvider servicesProvider
     */
    public function testServicesAreRegistered(array $services): void
    {
        $app = $this->createApp();

        foreach ($services as $service) {
            $this->assertNotNull($app->get($service));
        }
    }

    public function testConfigurator(): void
    {
        $app = $this->createApp();
        $configurator = $this->createConfigurator($app);
        $router = $this->createRouter($app);
        $options = ['name' => 'home', 'middleware' => 'auth', 'options' => ['paging' => true]];

        $configurator->configure(
            $router->get('/', MyController::class),
            $options
        );

        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();

        $route = $router->getRoutes()->getByName($options['name']);

        $this->assertNotNull($route);
        $this->assertTrue(in_array($options['middleware'], $route->getAction('middleware')));
        $this->assertSame($options['options'], $route->defaults);
    }

    public function testLoader(): void
    {
        $app = $this->createApp();
        $router = $this->createRouter($app);
        $resolver = $this->createResolver($app);
        $routes = [
            'app' => [
                'type'       => 'group',
                'prefix'     => '/app',
                'middleware' => ['auth'],
                'children'   => [
                    'dashboard' => [
                        'type'       => 'get',
                        'uri'        => '/dashboard',
                        'controller' => MyController::class,
                    ],
                ],
            ],
        ];

        $this->assertNotNull($loader = $resolver->resolve($routes));

        $loader->load($routes);

        $route = $router->getRoutes()->match(Request::create('/app/dashboard', 'GET'));

        $this->assertNotNull($route);
        $this->assertSame('app/dashboard', $route->uri);
        $this->assertContains('GET', $route->methods);
    }

    public function servicesProvider(): array
    {
        return [
            'Test Configurators' => [
                [
                    'routing.configurator.chain',
                    'routing.configurator.middleware',
                    'routing.configurator.name',
                    'routing.configurator.params',
                    RouteConfiguratorInterface::class,
                ],
            ],
            'Test Loaders'       => [
                [
                    'routing.resolver.loader',
                    'routing.loader.array',
                    'routing.loader.basic',
                    'routing.loader.file',
                    'routing.loader.group',
                    LoaderResolverInterface::class,
                ],
            ],
        ];
    }

    protected function createResolver(Application $app): LoaderResolverInterface
    {
        return $app->make(LoaderResolverInterface::class);
    }

    protected function createConfigurator(Application $app): RouteConfiguratorInterface
    {
        return $app->make(RouteConfiguratorInterface::class);
    }

    protected function createRouter(Application $app): Router
    {
        return $app->make('router');
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new RoutingBundle($app));

        $app->configure();
        $app->boot();

        return $app;
    }
}
