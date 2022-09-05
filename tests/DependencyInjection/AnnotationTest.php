<?php

declare(strict_types=1);

namespace Test\DependencyInjection;

use Pandawa\Bundle\AnnotationBundle\AnnotationBundle;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportInjectableAnnotationPlugin;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use PHPUnit\Framework\TestCase;
use Test\DependencyInjection\Service\ChildService;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AnnotationTest extends TestCase
{
    public function testService(): void
    {
        $app = $this->createApp();

        $single = $app->get('service.single');

        $this->assertNotNull($single);
        $this->assertTrue($single->isDebug());

        $depend = $app->get('service.depend');

        $this->assertNotNull($depend);
        $this->assertSame('DEBUG-PING', $depend->run());
        $this->assertNotNull($app->get('alias_service'));

        $child = $app->get(ChildService::class);
        $this->assertNotNull($child);

        $this->assertSame('dummy', $child->getName());
    }

    protected function createApp(): Application
    {
        $app = new Application();

        $app->register(new FoundationBundle($app));
        $app->register(new AnnotationBundle($app));
        $app->register(new DependencyInjectionBundle($app));

        $app['config']->set('debug', true);

        $app->register(new class($app) extends Bundle implements HasPluginInterface {

            public function plugins(): array
            {
                return [
                    new ImportInjectableAnnotationPlugin(),
                ];
            }
        });

        $app->configure();
        $app->boot();

        return $app;
    }
}
