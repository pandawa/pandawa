<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle;

use Illuminate\Contracts\Redis\Factory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Queue\Connectors\NullConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Queue\QueueServiceProvider;
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
    ];

    public function configure(): void
    {
        $this->app->singleton('queue', function ($app) {
            return tap(new QueueManager($app), function ($manager) {
                $this->registerConnectors($manager);
            });
        });
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
}
