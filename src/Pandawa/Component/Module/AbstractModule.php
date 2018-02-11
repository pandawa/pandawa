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

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
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

        $this->importConfigs();
        $this->importConsoles();
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

    protected function importConfigs(): void
    {
        $basePath = $this->getCurrentPath() . '/Resources/config';

        if (is_dir($basePath)) {
            $finder = new Finder();
            $configs = [];

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->name('*.php') as $file) {
                $configs[(string) $file] = config_path('modules/' . $file->getBasename());
            }

            $this->publishes($configs, 'config');
        }
    }

    protected function importConsoles(): void
    {
        $consolePath = $this->getCurrentPath() . '/Console';

        if (!is_dir($consolePath)) {
            return;
        }

        foreach (Finder::create()->in($consolePath)->name('*Console.php')->files() as $console) {
            $console = $this->getClassFromFile($console);

            if (is_subclass_of($console, Command::class)
                && !(new ReflectionClass($console))->isAbstract()) {
                Artisan::starting(
                    function ($artisan) use ($console) {
                        $artisan->resolve($console);
                    }
                );
            }
        }
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
