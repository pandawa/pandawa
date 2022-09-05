<?php

declare(strict_types=1);

namespace Test\Resource;

use Illuminate\Http\Request;
use Pandawa\Bundle\AnnotationBundle\AnnotationBundle;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\ResourceBundle\Plugin\ImportResourceAnnotationPlugin;
use Pandawa\Bundle\ResourceBundle\ResourceBundle;
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
class AnnotationTest extends TestCase
{
    public function testQueryResourceWithAnnotation(): void
    {
        $app = $this->createApp();

        $kernel = $app->make(Kernel::class);

        /** @var Response $response */
        $response = $kernel->handle(
            tap(
                Request::create('old-post', parameters: ['select' => 'title,content']),
                function ($request) {
                    $request->headers->set('Accept', 'application/json');
                    $request->headers->set('Content-Type', 'application/json');
                }
            )
        );

        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertSame(['title' => 'Very Old Post', 'content' => 'very old content'], $result['post'] ?? []);
    }

    protected function createApp(): Application
    {
        $app = new Application(__DIR__);
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new RoutingBundle($app));
        $app->register(new BusBundle($app));
        $app->register(new SerializerBundle($app));
        $app->register(new AnnotationBundle($app));
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
                    new ImportResourceAnnotationPlugin(),
                ];
            }
        });

        $app->configure();
        $app->boot();

        return $app;
    }
}
