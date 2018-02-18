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
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractModule extends ServiceProvider
{
    use ConfigProviderTrait, ConsoleProviderTrait, RuleProviderTrait;

    /**
     * @var array
     */
    protected $listen = [];

    /**
     * @var array
     */
    protected $configs = [];

    protected $servicePaths = [
        'Repository',
        'Service',
    ];

    public final function boot(): void
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->getTraits() as $trait) {
            $method = 'boot' . $trait;

            if (method_exists($this, $method)) {
                call_user_func([$this, $method]);
            }
        }

        $this->build();
    }

    public final function register(): void
    {
        foreach ($this->servicePaths as $path) {
            $servicePath = $this->getCurrentPath() . '/' . trim($path, '/');

            if (is_dir($servicePath) && is_readable($servicePath)) {
                foreach (Finder::create()->in($servicePath)->files() as $serviceFile) {
                    $serviceClass = $this->getClassFromFile($serviceFile);
                    $reflectionClass = new ReflectionClass($serviceClass);

                    if (count($reflectionClass->getInterfaces())) {
                        $this->app->singleton($reflectionClass->getInterfaces()[0], $serviceClass);
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

    public function listens(): array
    {
        return $this->listen;
    }

    protected function init(): void
    {
        // Override this method for custom initialization.
    }

    protected function build(): void
    {
        // Override this method for custom build.
    }

    protected function getCurrentPath(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return dirname($reflection->getFileName());
    }

    protected function getNamespace(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return $reflection->getNamespaceName();
    }

    protected function getNestedDirectory(SplFileInfo $file, $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }

    protected function getTraits(): Generator
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            $traitName = class_basename($trait);
            $traitName = preg_replace('/Trait$/', '', $traitName);

            yield $traitName;
        }
    }

    private function getClassFromFile(SplFileInfo $file): string
    {
        $className = $this->getNamespace() . '\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getPathname(), $this->getCurrentPath() . DIRECTORY_SEPARATOR)
            );

        return preg_replace('/\\+/', '\\', $className);
    }
}
