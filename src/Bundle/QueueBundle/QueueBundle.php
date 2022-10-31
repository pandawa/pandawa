<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle;

use Illuminate\Console\Application as Artisan;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Contracts\Queue\Monitor;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Queue\Connectors\NullConnector;
use Illuminate\Queue\Console\BatchesTableCommand;
use Illuminate\Queue\Console\ClearCommand as QueueClearCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\FlushFailedCommand as FlushFailedQueueCommand;
use Illuminate\Queue\Console\ForgetFailedCommand as ForgetFailedQueueCommand;
use Illuminate\Queue\Console\ListenCommand as QueueListenCommand;
use Illuminate\Queue\Console\ListFailedCommand as ListFailedQueueCommand;
use Illuminate\Queue\Console\MonitorCommand as QueueMonitorCommand;
use Illuminate\Queue\Console\PruneBatchesCommand as QueuePruneBatchesCommand;
use Illuminate\Queue\Console\PruneFailedJobsCommand as QueuePruneFailedJobsCommand;
use Illuminate\Queue\Console\RestartCommand as QueueRestartCommand;
use Illuminate\Queue\Console\RetryBatchCommand as QueueRetryBatchCommand;
use Illuminate\Queue\Console\RetryCommand as QueueRetryCommand;
use Illuminate\Queue\Console\TableCommand;
use Illuminate\Queue\Console\WorkCommand as QueueWorkCommand;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Queue\QueueManager;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Queue\Worker;
use Illuminate\Support\Facades\Facade;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Bundle\QueueBundle\Connector\BeanstalkdConnector;
use Pandawa\Bundle\QueueBundle\Connector\DatabaseConnector;
use Pandawa\Bundle\QueueBundle\Connector\RedisConnector;
use Pandawa\Bundle\QueueBundle\Connector\SqsConnector;
use Pandawa\Bundle\QueueBundle\Connector\SyncConnector;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use RuntimeException;


/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class QueueBundle extends Bundle implements HasPluginInterface, DeferrableProvider
{
    protected array $deferred = [
        'queue',
        'queue.connection',
        'queue.failer',
        QueueFactory::class,
        Monitor::class,
        Queue::class,
        FailedJobProviderInterface::class,
    ];

    protected array $aliases = [
        'queue' => [
            QueueFactory::class,
            Monitor::class,
        ],
        'queue.connection' => [
            Queue::class
        ],
        'queue.failer' => [
            FailedJobProviderInterface::class,
        ]
    ];

    protected array $commands = [
        'QueueClear'           => QueueClearCommand::class,
        'QueueFailed'          => ListFailedQueueCommand::class,
        'QueueFlush'           => FlushFailedQueueCommand::class,
        'QueueForget'          => ForgetFailedQueueCommand::class,
        'QueueListen'          => QueueListenCommand::class,
        'QueueMonitor'         => QueueMonitorCommand::class,
        'QueuePruneBatches'    => QueuePruneBatchesCommand::class,
        'QueuePruneFailedJobs' => QueuePruneFailedJobsCommand::class,
        'QueueRestart'         => QueueRestartCommand::class,
        'QueueRetry'           => QueueRetryCommand::class,
        'QueueRetryBatch'      => QueueRetryBatchCommand::class,
        'QueueWork'            => QueueWorkCommand::class,
    ];

    protected array $devCommands = [
        'QueueFailedTable'  => FailedTableCommand::class,
        'QueueTable'        => TableCommand::class,
        'QueueBatchesTable' => BatchesTableCommand::class,
    ];

    public function configure(): void
    {
        $this->app->singleton('queue', function ($app) {
            return tap(new QueueManager($app), function ($manager) {
                $this->registerConnectors($manager);
            });
        });

        foreach ($this->aliases as $abstract => $aliases) {
            foreach ($aliases as $alias) {
                $this->app->alias($abstract, $alias);
            }
        }

        $this->registerCommands();
        $this->registerWorker();
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new RegisterBundlesPlugin([
                QueueServiceProvider::class,
            ]),
        ];
    }

    protected function registerWorker(): void
    {
        $this->app->singleton('queue.worker', function ($app) {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            $resetScope = function () use ($app) {
                if (method_exists($app['log']->driver(), 'withoutContext')) {
                    $app['log']->withoutContext();
                }

                if ($app->bound('db')) {
                    if (method_exists($app['db'], 'getConnections')) {
                        foreach ($app['db']->getConnections() as $connection) {
                            $connection->resetTotalQueryDuration();
                            $connection->allowQueryDurationHandlersToRunAgain();
                        }
                    }
                }

                $app->forgetScopedInstances();

                Facade::clearResolvedInstances();
            };

            return new Worker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                $isDownForMaintenance,
                $resetScope
            );
        });
    }

    protected function registerConnectors(QueueManager $manager): void
    {
        foreach (['Null', 'Sync', 'Database', 'Redis', 'Beanstalkd', 'Sqs'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    protected function registerNullConnector(QueueManager $manager): void
    {
        $manager->addConnector('null', function () {
            return new NullConnector();
        });
    }

    protected function registerSyncConnector(QueueManager $manager): void
    {
        $manager->addConnector('sync', function () {
            return new SyncConnector();
        });
    }

    protected function registerDatabaseConnector(QueueManager $manager): void
    {
        $manager->addConnector('database', function () {
            if (!$this->app->bound('db')) {
                throw new RuntimeException('Please install pandawa/database-bundle to use queue with database.');
            }

            return new DatabaseConnector($this->app['db']);
        });
    }

    protected function registerRedisConnector(QueueManager $manager): void
    {
        $manager->addConnector('redis', function () {
            if (!$this->app->bound('redis') || !$this->app['redis'] instanceof Factory) {
                throw new RuntimeException('Please install pandawa/redis-bundle to use queue with redis.');
            }

            return new RedisConnector($this->app['redis']);
        });
    }

    protected function registerBeanstalkdConnector(QueueManager $manager): void
    {
        $manager->addConnector('beanstalkd', function () {
            return new BeanstalkdConnector();
        });
    }

    protected function registerSqsConnector(QueueManager $manager): void
    {
        $manager->addConnector('sqs', function () {
            return new SqsConnector();
        });
    }

    protected function registerCommands(): void
    {
        foreach (array_keys([...$this->commands, ...$this->devCommands]) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values([...$this->commands, ...$this->devCommands]));
    }

    protected function commands(array $commands): void
    {
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    protected function registerQueueFailedCommand(): void
    {
        $this->app->singleton(ListFailedQueueCommand::class);
    }

    protected function registerQueueForgetCommand(): void
    {
        $this->app->singleton(ForgetFailedQueueCommand::class);
    }

    protected function registerQueueFlushCommand(): void
    {
        $this->app->singleton(FlushFailedQueueCommand::class);
    }

    protected function registerQueueListenCommand(): void
    {
        $this->app->singleton(QueueListenCommand::class, function ($app) {
            return new QueueListenCommand($app['queue.listener']);
        });
    }

    protected function registerQueueMonitorCommand(): void
    {
        $this->app->singleton(QueueMonitorCommand::class, function ($app) {
            return new QueueMonitorCommand($app['queue'], $app['events']);
        });
    }

    protected function registerQueuePruneBatchesCommand(): void
    {
        $this->app->singleton(QueuePruneBatchesCommand::class, function () {
            return new QueuePruneBatchesCommand;
        });
    }

    protected function registerQueuePruneFailedJobsCommand(): void
    {
        $this->app->singleton(QueuePruneFailedJobsCommand::class, function () {
            return new QueuePruneFailedJobsCommand;
        });
    }

    protected function registerQueueRestartCommand(): void
    {
        $this->app->singleton(QueueRestartCommand::class, function ($app) {
            return new QueueRestartCommand($app['cache.store']);
        });
    }

    protected function registerQueueRetryCommand(): void
    {
        $this->app->singleton(QueueRetryCommand::class);
    }

    protected function registerQueueRetryBatchCommand(): void
    {
        $this->app->singleton(QueueRetryBatchCommand::class);
    }

    protected function registerQueueWorkCommand(): void
    {
        $this->app->singleton(QueueWorkCommand::class, function ($app) {
            return new QueueWorkCommand($app['queue.worker'], $app['cache.store']);
        });
    }

    protected function registerQueueClearCommand(): void
    {
        $this->app->singleton(QueueClearCommand::class);
    }

    protected function registerQueueFailedTableCommand(): void
    {
        $this->app->singleton(FailedTableCommand::class, function ($app) {
            return new FailedTableCommand($app['files'], $app['composer']);
        });
    }

    protected function registerQueueTableCommand(): void
    {
        $this->app->singleton(TableCommand::class, function ($app) {
            return new TableCommand($app['files'], $app['composer']);
        });
    }

    protected function registerQueueBatchesTableCommand(): void
    {
        $this->app->singleton(BatchesTableCommand::class, function ($app) {
            return new BatchesTableCommand($app['files'], $app['composer']);
        });
    }
}
