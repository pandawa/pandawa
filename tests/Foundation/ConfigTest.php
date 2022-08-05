<?php

declare(strict_types=1);

namespace Test\Foundation;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use Mockery;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Component\Foundation\Console\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Finder\Finder;
use Test\Foundation\Bundle\DemoBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigTest extends TestCase
{
    public function testLoadConfigDefinition(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $app = $this->createApp();
        $bundle = Mockery::mock(
            $this->createBundle(
                $app,
                new ImportConfigurationPlugin(basePath: 'test-config', configFilename: 'notfound')
            )
        );
        $app->register($bundle);
        $app->configure();
        $app->boot();
    }

    public function testLoadPhpConfig(): void
    {
        $app = $this->createApp();
        $bundle = Mockery::mock(
            $this->createBundle(
                $app,
                new ImportConfigurationPlugin(basePath: 'test-config', configFilename: 'php_config')
            )
        );
        $app->register($bundle);
        $app->configure();
        $app->boot();

        $config = $app['config'];

        $this->assertNotNull($config->get('pandawa'));
        $this->assertSame('Test App', $config->get('pandawa.name'));
        $this->assertSame(false, $config->get('pandawa.debug'));
    }

    public function testLoadYamlConfig(): void
    {
        $app = $this->createApp();
        $bundle = Mockery::mock(
            $this->createBundle(
                $app,
                new ImportConfigurationPlugin(basePath: 'test-config', configFilename: 'yaml_config')
            )
        );
        $app->register($bundle);
        $app->configure();
        $app->boot();

        $config = $app['config'];

        $this->assertNotNull($config->get('pandawa'));
        $this->assertSame('My App', $config->get('pandawa.name'));
        $this->assertSame(true, $config->get('pandawa.debug'));
    }

    public function testParseYamlConfig(): void
    {
        $app = $this->createApp();
        $redis = 'queue_redis';
        $keyFile = 'app_path';
        $basePath = __DIR__;
        $filename = 'report';
        $ext = 'pdf';

        Env::getRepository()->set('APP_DEBUG', 'false');
        Env::getRepository()->set('KEY_FILE', $keyFile);
        Env::getRepository()->set('BASE_PATH', $basePath);

        $config = $app['config'];
        $config->set('redis.default', $redis);
        $config->set('file.name', $filename);
        $config->set('file.ext', $ext);

        $bundle = Mockery::mock(
            $this->createBundle($app, new ImportConfigurationPlugin(basePath: 'test-config', definitionFilename: ''))
        );
        $app->register($bundle);
        $app->configure();
        $app->boot();

        $this->assertSame(false, $config->get('pandawa.debug'));
        $this->assertSame($redis, $config->get('pandawa.cache.redis'));
        $this->assertSame(
            $basePath.'/download-'.$filename.'.'.$ext,
            $config->get($bundle->getName().'.'.$keyFile)
        );
    }

    public function testPublishConfig(): void
    {
        ServiceProvider::$publishes = [];
        ServiceProvider::$publishGroups = [];
        \Illuminate\Console\Application::forgetBootstrappers();

        $app = $this->createApp(__DIR__);
        $app->singleton(KernelContract::class, Kernel::class);

        $bundle = new DemoBundle($app);
        $app->register(new FilesystemServiceProvider($app));
        $app->register($bundle);
        $app->configure();
        $app->boot();

        $artisan = $app->get(KernelContract::class);
        $artisan->registerCommand($app->make(VendorPublishCommand::class));

        $artisan->call('vendor:publish', ['--tag' => 'config']);

        $this->assertFileExists(config_path('packages/'.$bundle->getName().'.yaml'));
    }

    protected function createApp(?string $path = null): Application
    {
        $app = new Application($path);
        $app->instance('config', new Repository());

        return $app;
    }

    protected function createBundle(Application $app, ImportConfigurationPlugin $plugin): Bundle
    {
        return new class ($app, $plugin) extends Bundle {
            public function __construct(Application $app, protected Plugin $plugin)
            {
                parent::__construct($app);
            }

            public function getName(): string
            {
                return 'pandawa';
            }

            protected function plugins(): array
            {
                return [
                    $this->plugin,
                ];
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();

        $this->clearConfigFiles();
    }

    protected function clearConfigFiles(): void
    {
        foreach (
            Finder::create()->name(['*.php', '*.yaml'])->in(
                [__DIR__.'/bootstrap/cache', __DIR__.'/config/packages']
            )->files() as $file
        ) {
            unlink($file->getRealPath());
        }
    }
}
