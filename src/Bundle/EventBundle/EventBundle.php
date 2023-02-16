<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EventBundle;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Events\Dispatcher;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Event\EventBus;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Event\EventBusInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EventBundle extends Bundle implements HasPluginInterface
{
    const EVENT_CACHE_CONFIG_KEY = 'pandawa.events';

    public function boot(): void
    {
        $this->registerAliases();
    }

    public function configure(): void
    {
        $this->app->singleton('bus.event', function ($app) {
            return tap(new EventBus($app['bus.factory.envelope'], $app), function (EventBus $eventBus) use ($app) {
                $eventBus->setQueueResolver(function () use ($app) {
                    if (!$app->bound('queue')) {
                        throw new RuntimeException('Please install "pandawa/queue-bundle" to enable queue.');
                    }

                    return $app['queue'];
                });

                $eventBus->mergeMiddlewares($app['config']->get('event.middlewares', []));
            });
        });

        $this->registerAliases();
    }

    protected function registerAliases(): void
    {
        $this->app->alias('bus.event', EventBusInterface::class);
        $this->app->alias('bus.event', DispatcherContract::class);
        $this->app->alias('bus.event', Dispatcher::class);
        $this->app->alias('bus.event', 'events');
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
        ];
    }
}
