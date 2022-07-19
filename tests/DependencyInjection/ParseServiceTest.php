<?php

declare(strict_types=1);

namespace Test\DependencyInjection;

use Illuminate\Config\Repository;
use Pandawa\Component\DependencyInjection\Factory\ClassServiceFactory;
use Pandawa\Component\DependencyInjection\Factory\ConfigurationFactory;
use Pandawa\Component\DependencyInjection\Factory\FactoryResolver;
use Pandawa\Component\DependencyInjection\Factory\FactoryServiceFactory;
use Pandawa\Component\DependencyInjection\ServiceRegistry;
use Pandawa\Component\Foundation\Application;
use Pandawa\Contracts\DependencyInjection\Factory\ConfigurationFactoryInterface;
use Pandawa\Contracts\DependencyInjection\Factory\FactoryResolverInterface;
use PHPUnit\Framework\TestCase;
use Test\DependencyInjection\Factory\MyFactory;
use Test\DependencyInjection\Service\ChildService;
use Test\DependencyInjection\Service\ServiceManager;
use Test\DependencyInjection\Service\SingleService;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ParseServiceTest extends TestCase
{
    public function testSingleService(): void
    {
        $app = $this->createApp();
        $app['config']->set('debug', true);

        $registry = $app->make(ServiceRegistry::class);
        $registry->register('single', [
            'class'     => SingleService::class,
            'arguments' => [
                '%debug%',
            ],
        ]);

        $this->assertNotNull($app['single']);
        $this->assertInstanceOf(SingleService::class, $app['single']);
        $this->assertTrue($app['single']->isDebug());
    }

    public function testFactoryService(): void
    {
        $app = $this->createApp();
        $app['config']->set('debug', false);

        $app->singleton(MyFactory::class);

        $registry = $app->make(ServiceRegistry::class);
        $registry->register('single', [
            'factory'   => [sprintf('@%s', MyFactory::class), 'create'],
            'arguments' => [
                '%debug%',
            ],
        ]);

        $this->assertNotNull($app['single']);
        $this->assertInstanceOf(SingleService::class, $app['single']);
        $this->assertFalse($app['single']->isDebug());
    }

    public function testTagService(): void
    {
        $app = $this->createApp();

        $registry = $app->make(ServiceRegistry::class);
        $registry->register('manager', [
            'class'     => ServiceManager::class,
            'arguments' => [
                '#ChildService',
            ],
        ]);

        $registry->register('hello', [
            'class'     => ChildService::class,
            'arguments' => [
                'hello',
            ],
            'tag'       => 'ChildService',
        ]);

        $registry->register('world', [
            'class'     => ChildService::class,
            'arguments' => [
                'world',
            ],
            'tag'       => 'ChildService',
        ]);

        $this->assertTrue($app['manager']->hasService('hello'));
        $this->assertTrue($app['manager']->hasService('world'));
        $this->assertFalse($app['manager']->hasService('not_found'));
    }

    public function testLoadServices(): void
    {
        $app = $this->createApp();
        $app['config']->set('debug', false);

        $registry = $app->make(ServiceRegistry::class);
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
        $app->singleton(FactoryResolverInterface::class, fn() => new FactoryResolver([
            $app->make(ClassServiceFactory::class),
            $app->make(FactoryServiceFactory::class),
        ]));

        $app->singleton(ConfigurationFactoryInterface::class, ConfigurationFactory::class);

        return $app;
    }
}
