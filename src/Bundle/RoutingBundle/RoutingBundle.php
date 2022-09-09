<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle;

use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Foundation\Console\RouteClearCommand;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Routing\Router;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RoutingBundle extends Bundle implements HasPluginInterface
{
    const MIDDLEWARE_ALIASES_CONFIG_KEY = 'routing.middleware.aliases';
    const MIDDLEWARE_GROUPS_CONFIG_KEY = 'routing.middleware.groups';

    protected array $commands = [
        'RouteCache' => RouteCacheCommand::class,
        'RouteClear' => RouteClearCommand::class,
        'RouteList'  => RouteListCommand::class,
    ];

    public function register(): void
    {
        $this->booted(function () {
            if ($this->app->routesAreCached()) {
                $this->app->booted(function () {
                    require $this->app->getCachedRoutesPath();
                });
            }

            $this->loadMiddlewareAliasFromArray(
                $this->app['config']->get(
                    self::MIDDLEWARE_ALIASES_CONFIG_KEY,
                    []
                )
            );

            $this->loadMiddlewareGroupFromArray(
                $this->app['config']->get(
                    self::MIDDLEWARE_GROUPS_CONFIG_KEY,
                    []
                )
            );
        });

        $this->registerCommands();
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }

    protected function loadMiddlewareAliasFromArray(array $aliases): void
    {
        foreach ($aliases as $name => $middleware) {
            $this->router()->aliasMiddleware($name, $middleware);
        }
    }

    protected function loadMiddlewareGroupFromArray(array $groups): void
    {
        foreach ($groups as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $this->router()->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }

    protected function router(): Router
    {
        return $this->getService('router');
    }

    protected function registerCommands(): void
    {
        foreach (array_keys($this->commands) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values($this->commands));
    }

    protected function commands(array $commands): void
    {
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    protected function registerRouteCacheCommand(): void
    {
        $this->app->singleton(RouteCacheCommand::class, function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }

    protected function registerRouteClearCommand(): void
    {
        $this->app->singleton(RouteClearCommand::class, function ($app) {
            return new RouteClearCommand($app['files']);
        });
    }

    protected function registerRouteListCommand(): void
    {
        $this->app->singleton(RouteListCommand::class, function ($app) {
            return new RouteListCommand($app['router']);
        });
    }
}
