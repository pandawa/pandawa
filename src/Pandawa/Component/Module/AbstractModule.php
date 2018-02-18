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
    use ImportConfigTrait, ImportConsoleTrait, ImportRuleTrait;

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

    public function boot(): void
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot'.class_basename($trait);
            $method = preg_replace('/Trait$/', '', $method);

            if (method_exists($class, $method)) {
                call_user_func([$class, $method]);
            }
        }
    }

    public function register(): void
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
    }

    public function listens(): array
    {
        return $this->listen;
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
