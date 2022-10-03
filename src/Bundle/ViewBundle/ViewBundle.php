<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ViewBundle;

use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Console\ViewCacheCommand;
use Illuminate\Foundation\Console\ViewClearCommand;
use Illuminate\View\ViewServiceProvider;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ViewBundle extends Bundle implements HasPluginInterface
{
    protected array $aliases = [
        'view' => [
            \Illuminate\View\Factory::class,
            \Illuminate\Contracts\View\Factory::class,
        ],
    ];

    protected array $consoles = [
        'ViewCache' => ViewCacheCommand::class,
        'ViewClear' => ViewClearCommand::class,
    ];

    public function configure(): void
    {
        foreach ($this->aliases as $abstract => $aliases) {
            foreach ($aliases as $alias) {
                $this->app->alias($abstract, $alias);
            }
        }

        $this->registerConsoles();
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([
                ViewServiceProvider::class,
            ]),
            new ImportConfigurationPlugin(),
        ];
    }

    protected function registerConsoles(): void
    {
        foreach (array_keys($this->consoles) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values($this->consoles));
    }

    protected function commands(array $commands): void
    {
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    protected function registerViewCacheCommand(): void
    {
        $this->app->singleton(ViewCacheCommand::class);
    }

    protected function registerViewClearCommand(): void
    {
        $this->app->singleton(ViewClearCommand::class, function ($app) {
            return new ViewClearCommand($app['files']);
        });
    }
}
