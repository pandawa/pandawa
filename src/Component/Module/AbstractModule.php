<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Module;

use Generator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Pandawa\Component\Loader\ChainLoader;
use Pandawa\Component\Module\Provider\ConfigProviderTrait;
use Pandawa\Component\Module\Provider\ConsoleProviderTrait;
use Pandawa\Component\Module\Provider\MessageProviderTrait;
use Pandawa\Component\Module\Provider\PresenterProviderTrait;
use Pandawa\Component\Module\Provider\ResourceProviderTrait;
use Pandawa\Component\Module\Provider\RuleProviderTrait;
use Pandawa\Component\Module\Provider\ServiceProviderTrait;
use Pandawa\Component\Module\Provider\SpecificationProviderTrait;
use Pandawa\Component\Module\Provider\TransformerProviderTrait;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractModule extends ServiceProvider
{
    use ConfigProviderTrait, ConsoleProviderTrait, RuleProviderTrait, ServiceProviderTrait, PresenterProviderTrait;
    use ResourceProviderTrait, MessageProviderTrait, TransformerProviderTrait, SpecificationProviderTrait;

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * @var array
     */
    protected $scanServicePaths = [
        'Service',
        'Finder',
    ];

    /**
     * @var ChainLoader
     */
    protected $loader;

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->loader = ChainLoader::create();
    }

    public final function boot(): void
    {
        foreach ($this->getTraits() as $trait) {
            $method = 'boot' . $trait;

            if (method_exists($this, $method)) {
                call_user_func([$this, $method]);
            }
        }

        $this->build();
    }

    /**
     * @throws ReflectionException
     */
    public final function register(): void
    {
        foreach ($this->scanServicePaths as $path) {
            $servicePath = $this->getCurrentPath() . '/' . trim($path, '/');

            if (is_dir($servicePath) && is_readable($servicePath)) {
                foreach (Finder::create()->in($servicePath)->files() as $serviceFile) {
                    $serviceClass = $this->getClassFromFile($serviceFile);
                    $reflectionClass = new ReflectionClass($serviceClass);
                    $interfaces = $reflectionClass->getInterfaces();

                    if (1 === count($interfaces)) {
                        $interface = array_first($interfaces);
                        $this->app->singleton($interface->getName(), $serviceClass);
                    } else {
                        $this->app->singleton($serviceClass);
                    }
                }
            }
        }

        foreach ($this->getTraits() as $trait) {
            $method = 'register' . $trait;

            if (method_exists($this, $method)) {
                call_user_func([$this, $method]);
            }
        }

        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    protected function mergeConfigFrom($path, $key)
    {
        $this->mergeConfig($key, $this->loader->load($path));
    }

    /**
     * Merge config to given key.
     *
     * @param string $key
     * @param array  $configs
     */
    protected function mergeConfig(string $key, array $configs): void
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_merge($configs, $config));
    }

    protected function init(): void
    {
        // Override this method for custom initialization.
    }

    protected function build(): void
    {
        // Override this method for custom build.
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    protected function getCurrentPath(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return dirname($reflection->getFileName());
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    protected function getNamespace(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return $reflection->getNamespaceName();
    }

    /**
     * @param SplFileInfo $file
     * @param             $configPath
     *
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }

    /**
     * @return Generator
     */
    protected function getTraits(): Generator
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            $traitName = class_basename($trait);
            $traitName = preg_replace('/Trait$/', '', $traitName);

            yield $traitName;
        }
    }

    /**
     * @param SplFileInfo $file
     *
     * @return string
     * @throws ReflectionException
     */
    protected function getClassFromFile(SplFileInfo $file): string
    {
        $className = $this->getNamespace() . '\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getPathname(), $this->getCurrentPath() . '/')
            );

        return preg_replace('/\\+/', '\\', $className);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    protected function getModuleName(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return Str::kebab(preg_replace('/Module$/', '', $reflection->getShortName()));
    }
}
