<?php

declare(strict_types=1);

namespace Test\Bus;

use Illuminate\Container\Container;
use Monolog\Test\TestCase;
use Pandawa\Component\Bus\MessageRegistry;
use Pandawa\Component\Bus\Stamp\QueuedStamp;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Test\Bus\Command\CreatePost;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageRegistryTest extends TestCase
{
    public function testAddMessage(): void
    {
        $registry = new MessageRegistry(new Container());
        $registry->add($messageClass = CreatePost::class, $message = [
            'name'   => 'post.create',
            'stamps' => [
                [
                    'class'     => QueuedStamp::class,
                    'arguments' => [
                        'queue' => 'post',
                        'delay' => 100,
                    ],
                ],
            ],
        ]);

        $this->asserts($registry, $messageClass, $message);
    }

    public function testLoadMessages(): void
    {
        $messageClass = CreatePost::class;
        $message = [
            'name'   => 'post.create',
            'stamps' => [
                [
                    'class'     => QueuedStamp::class,
                    'arguments' => [
                        'queue' => 'post',
                        'delay' => 100,
                    ],
                ],
            ],
        ];

        $registry = new MessageRegistry(new Container());
        $registry->load([
            $messageClass => $message,
        ]);

        $this->asserts($registry, $messageClass, $message);
    }

    protected function asserts(RegistryInterface $registry, string $messageClass, array $message): void
    {
        $this->assertTrue($registry->has($messageClass));
        $this->assertTrue($registry->hasName($message['name']));
        $this->assertNotNull($metadata = $registry->getByName($message['name']));
        $this->assertSame($message['name'], $metadata->name);
        $this->assertSame($messageClass, $metadata->class);
        $this->assertCount(count($message['stamps']), $metadata->stamps);
        $this->assertSame($message['stamps'][0]['arguments']['queue'], $metadata->stamps[0]->queue);
        $this->assertSame($message['stamps'][0]['arguments']['delay'], $metadata->stamps[0]->delay);
    }
}
