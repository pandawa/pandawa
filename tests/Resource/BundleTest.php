<?php

declare(strict_types=1);

namespace Test\Resource;

use Illuminate\Http\Request;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Bundle\BusBundle\Plugin\ImportMessagesPlugin;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\ResourceBundle\ResourceBundle;
use Pandawa\Bundle\RoutingBundle\Plugin\ImportRoutesPlugin;
use Pandawa\Bundle\RoutingBundle\RoutingBundle;
use Pandawa\Bundle\SerializerBundle\SerializerBundle;
use Pandawa\Bundle\TranslationBundle\TranslationBundle;
use Pandawa\Bundle\ValidationBundle\ValidationBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\Http\Kernel;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    /**
     * @dataProvider servicesProvider
     */
    public function testServiceRegistered(array $services): void
    {
        $app = $this->createApp();

        foreach ($services as $service) {
            $this->assertNotNull($app->get($service));
        }
    }

    public function testCommandResource(): void
    {
        $app = $this->createApp();

        $kernel = $app->make(Kernel::class);

        $data = ['title' => 'New post', 'content' => 'hello world'];

        /** @var Response $response */
        $response = $kernel->handle(
            tap(Request::create('posts', 'POST', content: json_encode($data)), function ($request) {
                $request->headers->set('Accept', 'application/json');
                $request->headers->set('Content-Type', 'application/json');
            })
        );

        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('post', $result);
        $this->assertSame(['title' => $data['title']], $result['post'] ?? []);
    }

    public function testQueryResource(): void
    {
        $app = $this->createApp();

        $kernel = $app->make(Kernel::class);

        /** @var Response $response */
        $response = $kernel->handle(
            tap(
                Request::create('posts', parameters: ['select' => 'title,content']),
                function ($request) {
                    $request->headers->set('Accept', 'application/json');
                    $request->headers->set('Content-Type', 'application/json');
                }
            )
        );

        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertSame(['title' => 'Old Post', 'content' => 'old content'], $result['post'] ?? []);

        /** @var Response $response */
        $response = $kernel->handle(
            tap(
                Request::create('posts', parameters: ['select' => 'content']),
                function ($request) {
                    $request->headers->set('Accept', 'application/json');
                    $request->headers->set('Content-Type', 'application/json');
                }
            )
        );

        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertSame(['content' => 'old content'], $result['post'] ?? []);
    }

    public function servicesProvider(): array
    {
        return [
            'Test Formatters' => [
                [
                    'resource.formatter.resolver',
                    'Pandawa\Contracts\Resource\Formatter\FormatterResolverInterface',
                    'resource.formatter.csv',
                    'resource.formatter.json',
                    'resource.formatter.xml',
                    'resource.formatter.yaml',
                ]
            ],
            'Test Model Handlers' => [
                [
                    'resource.model_handler.factory_resolver',
                    'Pandawa\Contracts\Resource\Model\FactoryResolverInterface',
                    'resource.model_handler.eloquent.handler_factory',
                ]
            ],
            'Test Routing' => [
                [
                    'resource.loader.message',
                    'resource.loader.resource',
                    'resource.configurator.route_message',
                    'resource.configurator.route_resource',
                ]
            ],
            'Test Renderer' => [
                [
                    'resource.renderer',
                    'Pandawa\Contracts\Resource\RendererInterface',
                ]
            ],
        ];
    }

    protected function createApp(): Application
    {
        $app = new Application(__DIR__);
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new RoutingBundle($app));
        $app->register(new BusBundle($app));
        $app->register(new SerializerBundle($app));
        $app->register(new ResourceBundle($app));
        $app->register(new ValidationBundle($app));
        $app->register(new TranslationBundle($app));

        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Pandawa\Component\Foundation\Handler\ExceptionHandler::class,
        );

        $app->register(new class($app) extends Bundle implements HasPluginInterface {
            public function plugins(): array
            {
                return [
                    new ImportMessagesPlugin(),
                    new ImportRoutesPlugin(),
                ];
            }
        });

        $app->configure();
        $app->boot();

        return $app;
    }
}
