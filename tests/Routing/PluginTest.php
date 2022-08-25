<?php

declare(strict_types=1);

namespace Test\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\RoutingBundle\Plugin\ImportRoutesPlugin;
use Pandawa\Bundle\RoutingBundle\RoutingBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PluginTest extends TestCase
{
    public function testImport(): void
    {
        $app = $this->createApp();
        $app->register(
            new class($app) extends Bundle implements HasPluginInterface {
                public function plugins(): array
                {
                    return [
                        new ImportRoutesPlugin(),
                    ];
                }
            }
        );

        $app->configure();
        $app->boot();

        $router = $this->createRouter($app);
        $router->getRoutes()->refreshNameLookups();

        $this->assertNotNull($router->getRoutes()->match(Request::create('/ping', 'GET')));
        $this->assertNotNull($router->getRoutes()->match(Request::create('/app/dashboard', 'GET')));
    }

    protected function createRouter(Application $app): Router
    {
        return $app->get('router');
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new RoutingBundle($app));

        return $app;
    }
}
