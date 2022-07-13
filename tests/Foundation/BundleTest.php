<?php

declare(strict_types=1);

namespace Test\Foundation;

use Mockery;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use PHPUnit\Framework\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    public function testRegisterBundle(): void
    {
        $app = $this->createApp();
        $bundle = Mockery::mock(Bundle::class);

        $bundle->expects('configurePlugin');
        $bundle->expects('bootPlugin');
        $bundle->expects('register');
        $bundle->expects('configure');
        $bundle->expects('boot');
        $bundle->expects('callBootingCallbacks');
        $bundle->expects('callBootedCallbacks');

        $app->register($bundle);
        $app->boot();

        $this->assertArrayHasKey(get_class($bundle), $app->getLoadedProviders());
    }

    protected function createApp(): Application
    {
        $app = new Application();

        $config = Mockery::mock(\stdClass::class);
        $config->shouldReceive('get')->andReturn([]);

        $app['config'] = $config;

        return $app;
    }

    public function testRegisterPlugin(): void
    {
        $this->expectNotToPerformAssertions();

        $app = $this->createApp();
        $bundle = Mockery::mock(new class ($app) extends Bundle {
            protected function plugins(): array
            {
                $plugin = Mockery::mock(Plugin::class);
                $plugin->expects('setBundle');
                $plugin->expects('configure');
                $plugin->expects('boot');

                return [
                    $plugin,
                ];
            }
        });

        $app->register($bundle);
        $app->boot();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
