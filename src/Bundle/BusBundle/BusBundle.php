<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle;

use Illuminate\Bus\BatchFactory;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BusBundle extends Bundle implements HasPluginInterface
{
    const MESSAGE_CONFIG_KEY = 'bus.messages';
    const HANDLER_CONFIG_KEY = 'bus.handlers';

    const PANDAWA_MESSAGE_CONFIG_KEY = 'pandawa.messages';
    const PANDAWA_HANDLER_CONFIG_KEY = 'pandawa.handlers';

    public function boot(): void
    {
        $this->app->booted(function () {
            $this->registry()->load(
                $this->config()->get(static::MESSAGE_CONFIG_KEY, [])
            );

            $this->bus()->map(
                $this->config()->get(static::HANDLER_CONFIG_KEY, [])
            );
        });
    }

    public function register(): void
    {
        $this->app->singleton(BatchRepository::class, DatabaseBatchRepository::class);

        $this->app->singleton(DatabaseBatchRepository::class, function ($app) {
            return new DatabaseBatchRepository(
                $app->make(BatchFactory::class),
                $app->make('db')->connection($app->config->get('queue.batching.database')),
                $app->config->get('queue.batching.table', 'job_batches')
            );
        });
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }

    protected function config(): Config
    {
        return $this->app['config'];
    }

    protected function registry(): RegistryInterface
    {
        return $this->app[RegistryInterface::class];
    }

    protected function bus(): BusInterface
    {
        return $this->app[BusInterface::class];
    }
}
