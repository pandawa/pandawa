<?php

declare(strict_types=1);

namespace Test\Bus;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Contracts\Queue\Queue;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Bundle\BusBundle\Plugin\ImportMessagesPlugin;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Bus\QueueFactoryInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use PHPUnit\Framework\TestCase;
use Test\Bus\Command\CalculateSomething;
use Test\Bus\Command\CreatePost;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    public function testServicesAreRegistered(): void
    {
        $services = [
            'bus.message.registry',
            'bus.factory.queue',
            'bus.default',
            RegistryInterface::class,
            QueueFactoryInterface::class,
            BusInterface::class,
            Dispatcher::class,
            QueueingDispatcher::class,
        ];
        $app = $this->createApp();
        $app->configure();
        $app->boot();

        foreach ($services as $service) {
            $this->assertNotNull($app->get($service));
        }
    }

    public function testPlugin(): void
    {
        $app = $this->createApp();
        $app->register(new class($app) extends Bundle implements HasPluginInterface {
            public function plugins(): array
            {
                return [
                    new ImportMessagesPlugin(),
                ];
            }
        });

        $app->configure();

        $app->instance(QueueFactoryInterface::class, new class implements QueueFactoryInterface {
            public function create(string $connection): Queue
            {
                $queue = \Mockery::mock(Queue::class);

                if ('delayed' === $connection) {
                    $queue->shouldReceive('later')->andReturn('delayed');
                }

                return $queue;
            }

            public function supports(): bool
            {
                return true;
            }
        });

        $app->boot();

        /** @var BusInterface $bus */
        $bus = $app->get(BusInterface::class);

        $this->assertSame('Title:Plugin Test', $bus->dispatch(new CreatePost('Plugin Test')));
        $this->assertSame('delayed', $bus->dispatch(new CalculateSomething()));
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new BusBundle($app));

        return $app;
    }
}
