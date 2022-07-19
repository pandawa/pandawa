<?php

declare(strict_types=1);

namespace Test\DependencyInjection;

use Illuminate\Config\Repository;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Contracts\DependencyInjection\Factory\ConfigurationFactoryInterface;
use Pandawa\Contracts\DependencyInjection\Factory\FactoryResolverInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use PHPUnit\Framework\TestCase;
use Test\DependencyInjection\Factory\MyFactory;
use Test\DependencyInjection\Service\SingleService;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    public function testRegisteredServices(): void
    {
        $app = $this->createApp();

        $this->assertNotNull($app->get(ConfigurationFactoryInterface::class));
        $this->assertNotNull($app->get(FactoryResolverInterface::class));
        $this->assertNotNull($app->get(ServiceRegistryInterface::class));
    }

    public function testLoadService(): void
    {
        $app = $this->createApp();
        $registry = $app->get(ServiceRegistryInterface::class);
        $registry->load([
            'single'         => [
                'class'     => SingleService::class,
                'arguments' => [
                    '%debug%',
                ],
            ],
            'my_factory'     => [
                'class' => MyFactory::class,
            ],
            'single_factory' => [
                'factory'   => ['@my_factory', 'create'],
                'arguments' => [
                    '%debug%',
                ],
            ],
        ]);

        $this->assertNotNull($app['single']);
        $this->assertNotNull($app['my_factory']);
        $this->assertNotNull($app['single_factory']);
    }

    protected function createApp(?string $path = null): Application
    {
        $app = new Application($path);
        $app->instance('config', new Repository());

        $app->register(new DependencyInjectionBundle($app));
        $app->configure();
        $app->boot();

        return $app;
    }
}
