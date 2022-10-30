<?php

declare(strict_types=1);

namespace Test\Queue;

use Illuminate\Queue\Events\JobProcessed;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Bundle\DatabaseBundle\DatabaseBundle;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\QueueBundle\Queue\DatabaseQueue;
use Pandawa\Bundle\QueueBundle\Queue\RedisQueue;
use Pandawa\Bundle\QueueBundle\Queue\SyncQueue;
use Pandawa\Bundle\QueueBundle\QueueBundle;
use Pandawa\Bundle\RedisBundle\RedisBundle;
use Pandawa\Bundle\SerializerBundle\SerializerBundle;
use Pandawa\Component\Bus\Stamp\QueuedStamp;
use Pandawa\Component\Foundation\Application;
use Pandawa\Contracts\Bus\QueueFactoryInterface;
use PHPUnit\Framework\TestCase;
use Test\Bus\Command\CreatePost;
use Test\Bus\Handler\CreatePostHandler;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    public function testQueueReplaced(): void
    {
        $app = $this->createApp();
        $factory = $app[QueueFactoryInterface::class];
        $maps = [
            'sync'     => SyncQueue::class,
            'database' => DatabaseQueue::class,
            'redis'    => RedisQueue::class,
        ];

        foreach ($maps as $connection => $queueClass) {
            $this->assertInstanceOf($queueClass, $factory->create($connection));
        }
    }

    public function testQueue(): void
    {

        $app = $this->createApp();
        $payload = [];

        $app['bus.registry']->load([
            CreatePost::class => [
                'name' => 'greeting',
                'stamps' => [
                    [
                        'class' => QueuedStamp::class,
                        'arguments' => [
                            'connection' => 'sync',
                        ]
                    ]
                ]
            ]
        ]);
        $app['bus.default']->map(['greeting' => CreatePostHandler::class]);

        $app['events']->listen(JobProcessed::class, function (JobProcessed $event) use (&$payload) {
            $payload = $event->job->payload()['data'];
        });


        $app['bus.default']->dispatch(new CreatePost('Hello World'));

        $this->assertSame('greeting', $payload['commandName']);
        $this->assertSame('Hello World', $payload['command']['serialized']['title']);
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new QueueBundle($app));
        $app->register(new SerializerBundle($app));
        $app->register(new BusBundle($app));
        $app->register(new DatabaseBundle($app));
        $app->register(new RedisBundle($app));

        $app->configure();
        $app->boot();

        return $app;
    }
}
