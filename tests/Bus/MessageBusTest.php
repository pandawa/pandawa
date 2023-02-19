<?php

declare(strict_types=1);

namespace Test\Bus;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Queue;
use Pandawa\Component\Bus\Factory\EnvelopeFactory;
use Pandawa\Component\Bus\MessageBus;
use Pandawa\Component\Bus\MessageRegistry;
use Pandawa\Component\Bus\Middleware\RunInDatabaseTransactionMiddleware;
use Pandawa\Component\Bus\Stamp\QueuedStamp;
use Pandawa\Contracts\Bus\QueueFactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Test\Bus\Command\BigCalculateSomething;
use Test\Bus\Command\CalculateSomething;
use Test\Bus\Command\CreateComment;
use Test\Bus\Command\CreatePost;
use Test\Bus\Handler\CalculateSomethingHandler;
use Test\Bus\Handler\CreatePostHandler;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageBusTest extends TestCase
{
    public function testSyncHandler(): void
    {
        $bus = $this->createMessageBus();

        $result = $bus->dispatch(new CreatePost('Hello World'));

        $this->assertSame('Title:Hello World', $result);
    }

    public function testSelHandler(): void
    {
        $bus = $this->createMessageBus();
        $result = $bus->dispatch(new CreateComment('Test'));

        $this->assertSame('Comment:Test', $result);
    }

    public function testQueue(): void
    {
        $bus = $this->createMessageBus();

        $this->assertSame('delayed', $bus->dispatch(new CalculateSomething()));
        $this->assertSame('calculated', $bus->dispatchNow(new CalculateSomething()));
        $this->assertSame('big delayed', $bus->dispatch(new BigCalculateSomething()));
    }

    protected function createMessageBus(): MessageBus
    {
        return new MessageBus(
            new Container(),
            new EnvelopeFactory($this->createRegistry(), new Serializer()),
            new class implements QueueFactoryInterface {

                public function create(string $connection): Queue
                {
                    $queue = \Mockery::mock(Queue::class);

                    if ('delayed' === $connection) {
                        $queue->shouldReceive('later')->andReturn('delayed');
                    }

                    if ('big_delayed' === $connection) {
                        $queue->shouldReceive('laterOn')->andReturn('big delayed');
                    }

                    return $queue;
                }

                public function supports(): bool
                {
                    return true;
                }
            },
            [
                RunInDatabaseTransactionMiddleware::class,
            ],
            [
                'post.create' => CreatePostHandler::class,
                CalculateSomething::class => CalculateSomethingHandler::class,
            ]
        );
    }

    protected function createRegistry(): MessageRegistry
    {
        $registry = new MessageRegistry(
            new Container(),
            Serializer::class
        );
        $registry->load([
            CreatePost::class => [
                'name' => 'post.create',
            ],
            CalculateSomething::class => [
                'stamps' => [
                    [
                        'class' => QueuedStamp::class,
                        'arguments' => [
                            'connection' => 'delayed',
                            'delay' => 10,
                        ]
                    ]
                ]
            ],
            BigCalculateSomething::class => [
                'stamps' => [
                    [
                        'class' => QueuedStamp::class,
                        'arguments' => [
                            'connection' => 'big_delayed',
                            'queue' => 'big',
                            'delay' => 100,
                        ]
                    ]
                ]
            ],
        ]);

        return $registry;
    }
}
