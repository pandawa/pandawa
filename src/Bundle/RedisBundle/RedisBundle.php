<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RedisBundle;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisBundle extends Bundle implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton('redis', function ($app) {
            $config = $app->make('config');

            $options = [
                ...Arr::except($config->get('redis', []), ['connections']),
                ...$config->get('redis.connections', [])
            ];

            return new RedisManager($app, $options['client'], $options);
        });
    }

    protected function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }

    public function provides(): array
    {
        return ['redis'];
    }
}
