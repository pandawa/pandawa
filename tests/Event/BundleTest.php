<?php

declare(strict_types=1);

namespace Test\Event;

use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\EventBundle\EventBundle;
use Pandawa\Bundle\EventBundle\Plugin\ImportEventListenerPlugin;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\SerializerBundle\SerializerBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Event\EventBusInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    public function testDispatchEvent(): void
    {
        $app = $this->createApp();
        $title = 'Hello World';
        $bus = $this->bus($app);

        $this->assertSame(
            'Post Created: ' . $title,
            $bus->dispatch(new PostCreated($title), [], true)
        );
    }

    protected function bus(Application $app): EventBusInterface
    {
        return $app->get('events');
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new BusBundle($app));
        $app->register(new EventBundle($app));
        $app->register(new SerializerBundle($app));

        $app->register(new class($app) extends Bundle implements HasPluginInterface{
            public function plugins(): array
            {
                return [
                    new ImportEventListenerPlugin(),
                ];
            }
        });

        $app->configure();
        $app->boot();

        return $app;
    }
}
