<?php

declare(strict_types=1);

namespace Test\Foundation;

use Illuminate\Config\Repository;
use Mockery;
use Pandawa\Bundle\FoundationBundle\Plugin\LoadConfigurationPlugin;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigTest extends TestCase
{
    public function testConfigDefinition(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $app = $this->createApp();
        $bundle = Mockery::mock($this->createBundle($app, new LoadConfigurationPlugin(basePath: 'config', configFilename: 'notfound')));
        $app->register($bundle);
        $app->boot();
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->instance('config', new Repository());

        return $app;
    }

    protected function createBundle(Application $app, LoadConfigurationPlugin $plugin): Bundle
    {
        return new class ($app, $plugin) extends Bundle {
            public function __construct(Application $app, protected Plugin $plugin)
            {
                parent::__construct($app);
            }

            public function getName(): string
            {
                return 'test';
            }

            protected function plugins(): array
            {
                return [
                    $this->plugin,
                ];
            }
        };
    }

    public function testConfig(): void
    {
        $app = $this->createApp();
        $bundle = Mockery::mock($this->createBundle($app, new LoadConfigurationPlugin(basePath: 'config')));
        $app->register($bundle);
        $app->boot();

        $config = $app['config'];

        $this->assertNotNull($config->get('test'));
        $this->assertSame('Test App', $config->get('test.name'));
        $this->assertSame(false, $config->get('test.debug'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
