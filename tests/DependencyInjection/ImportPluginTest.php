<?php

declare(strict_types=1);

namespace Test\DependencyInjection;

use Illuminate\Config\Repository;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportPluginTest extends TestCase
{
    public function testImportServices(): void
    {
        $app = $this->createApp();
        $app->register(
            new class($app) extends Bundle implements HasPluginInterface {
                public function plugins(): array
                {
                    return [
                        new ImportServicesPlugin(),
                    ];
                }
            }
        );
        $app->configure();
        $app->boot();

        $this->assertNotNull($app['single']);
        $this->assertNotNull($app['my_factory']);
        $this->assertNotNull($app['single_factory']);
    }

    protected function createApp(?string $path = null): Application
    {
        $app = new Application($path);
        $app->instance('config', new Repository());

        $app->register(new DependencyInjectionBundle($app));

        return $app;
    }
}
