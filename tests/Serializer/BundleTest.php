<?php

declare(strict_types=1);

namespace Test\Serializer;

use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\SerializerBundle\SerializerBundle;
use Pandawa\Component\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    public function testServiceRegistered(): void
    {
        $app = $this->createApp();

        $serializers = config('serializer.serializers');

        foreach (array_keys($serializers) as $serializer) {
            $this->assertNotNull($app->get(sprintf('serializer.%s', $serializer)));
        }

        $this->assertNotNull($app->get(SerializerInterface::class));
    }

    public function testSerializeObject(): void
    {
        $post = new Post('New Post', 'hello world');
        $app = $this->createApp();

        /** @var SerializerInterface $serializer */
        $serializer = $app->get('serializer.default');

        $expect = json_encode(['title' => $post->title, 'content' => $post->content]);

        $this->assertSame($expect, $serializer->serialize($post, 'json'));
    }

    public function testDeserializeObject(): void
    {
        $data = ['title' => 'New Post', 'content' => 'hello world'];
        $app = $this->createApp();

        /** @var SerializerInterface $serializer */
        $serializer = $app->get('serializer.default');
        $post = $serializer->deserialize(json_encode($data), Post::class, 'json');

        $this->assertSame($data['title'], $post->title);
        $this->assertSame($data['content'], $post->content);
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new SerializerBundle($app));

        $app->configure();
        $app->boot();

        return $app;
    }
}
