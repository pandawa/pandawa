<?php

declare(strict_types=1);

namespace Pandawa\Bundle\CacheBundle;

use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Cache\Console\ClearCommand as CacheClearCommand;
use Illuminate\Cache\Console\ForgetCommand as CacheForgetCommand;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CacheBundle extends Bundle implements DeferrableProvider
{
    protected array $commands = [
        'CacheClear'  => CacheClearCommand::class,
        'CacheForget' => CacheForgetCommand::class,
    ];

    protected array $devCommands = [
        'CacheTable' => CacheTableCommand::class,
    ];

    public function configure(): void
    {
        $this->app->singleton(RateLimiter::class, function (Application $app) {
            return new RateLimiter(
                $app->make('cache')->driver(
                    $app['config']->get('cache.limiter')
                )
            );
        });

        $this->registerCommands();
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
        return [RateLimiter::class];
    }

    protected function registerCommands(): void
    {
        foreach (array_keys([...$this->commands, ...$this->devCommands]) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->app->loadConsoles(array_values([...$this->commands, ...$this->devCommands]));
    }

    protected function registerCacheForgetCommand(): void
    {
        $this->app->singleton(CacheForgetCommand::class, function ($app) {
            return new CacheForgetCommand($app['cache']);
        });
    }

    protected function registerCacheClearCommand(): void
    {
        $this->app->singleton(CacheClearCommand::class, function ($app) {
            return new CacheClearCommand($app['cache'], $app['files']);
        });
    }

    protected function registerCacheTableCommand(): void
    {
        $this->app->singleton(CacheTableCommand::class, function ($app) {
            return new CacheTableCommand($app['files'], $app['composer']);
        });
    }
}
