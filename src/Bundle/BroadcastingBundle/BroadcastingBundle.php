<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BroadcastingBundle;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Contracts\Broadcasting\Broadcaster as BroadcasterContract;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Event\EventBusInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Aldi Arief <aldiarief598@gmail.com>
 */
class BroadcastingBundle extends Bundle implements HasPluginInterface, DeferrableProvider
{
    protected array $deferred = [
        BroadcastManager::class,
        BroadcastingFactory::class,
        BroadcasterContract::class,
    ];

    public function boot(): void
    {
        $this->event()->listen('*', $this->config()->get('broadcasting.handler'));
    }

    public function configure(): void
    {
        $this->app->singleton(BroadcastManager::class, function ($app) {
            return new BroadcastManager($app);
        });

        $this->app->singleton(BroadcasterContract::class, function ($app) {
            return $app->make(BroadcastManager::class)->connection();
        });

        $this->app->alias(
            BroadcastManager::class, BroadcastingFactory::class
        );
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new RegisterBundlesPlugin([
                BroadcastServiceProvider::class,
            ]),
        ];
    }

    protected function config(): Config
    {
        return $this->app['config'];
    }

    protected function event(): EventBusInterface
    {
        return $this->app['events'];
    }
}
