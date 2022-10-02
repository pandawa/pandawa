<?php

declare(strict_types=1);

namespace Pandawa\Bundle\HorizonBundle;

use Exception;
use Illuminate\Console\Application as Artisan;
use Illuminate\Queue\QueueManager;
use Laravel\Horizon\Console;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\EventBundle\Plugin\ImportEventListenerPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\HorizonBundle\Connector\RedisConnector;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class HorizonBundle extends Bundle implements HasPluginInterface
{
    public function boot(): void
    {
        $this->registerConsoles();
    }

    public function configure(): void
    {
        $this->app->bind(Console\WorkCommand::class, function ($app) {
            return new Console\WorkCommand($app['queue.worker'], $app['cache.store']);
        });

        $this->useRedis(config('horizon.use', 'default'));
        $this->registerQueueConnectors();
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
            new ImportEventListenerPlugin(),
        ];
    }

    protected function registerQueueConnectors(): void
    {
        $this->callAfterResolving(QueueManager::class, function ($manager) {
            $manager->addConnector('redis', function () {
                return new RedisConnector($this->app['redis']);
            });
        });
    }

    protected function registerConsoles(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\ClearCommand::class,
                Console\ContinueCommand::class,
                Console\ContinueSupervisorCommand::class,
                Console\ForgetFailedCommand::class,
                Console\HorizonCommand::class,
                Console\ListCommand::class,
                Console\PauseCommand::class,
                Console\PauseSupervisorCommand::class,
                Console\PurgeCommand::class,
                Console\StatusCommand::class,
                Console\SupervisorCommand::class,
                Console\SupervisorsCommand::class,
                Console\TerminateCommand::class,
                Console\TimeoutCommand::class,
                Console\WorkCommand::class,
            ]);
        }

        $this->commands([Console\SnapshotCommand::class]);
    }

    protected function commands(array $commands): void
    {
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    protected function useRedis(string $connection): void
    {
        if (! is_null($config = config("redis.clusters.{$connection}.0"))) {
            config(["redis.{$connection}" => $config]);
        } elseif (is_null($config) && is_null($config = config("redis.connections.{$connection}"))) {
            throw new Exception("Redis connection [{$connection}] has not been configured.");
        }

        $config['options']['prefix'] = config('horizon.prefix') ?: 'horizon:';

        config(['redis.connections.horizon' => $config]);
    }
}
