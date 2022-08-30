<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Bundle;

use Closure;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Str;
use Pandawa\Component\Foundation\Application;
use Pandawa\Contracts\Foundation\BundleInterface as BundleContract;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use Pandawa\Contracts\Foundation\PluginInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Bundle implements BundleContract
{
    /**
     * All of the registered booting callbacks.
     *
     * @var callable[]
     */
    protected array $bootingCallbacks = [];

    /**
     * All of the registered booted callbacks.
     *
     * @var callable[]
     */
    protected array $bootedCallbacks = [];

    /**
     * Reflection cache.
     *
     * @var ReflectionClass|null
     */
    private ?ReflectionClass $reflection = null;

    /**
     * @var PluginInterface[]
     */
    private array $initializedPlugins = [];

    public function __construct(protected Application $app)
    {
        if ($this instanceof HasPluginInterface) {
            foreach ($this->plugins() as $plugin) {
                $this->initializedPlugins[] = tap(
                    $plugin,
                    fn(PluginInterface $plugin) => $plugin->setBundle($this)
                );
            }
        }
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function getService(string $name): mixed
    {
        return $this->app->get($name);
    }

    public function getName(): string
    {
        return $this->transformName($this->getReflection()->getShortName());
    }

    public function getNamespace(): string
    {
        return $this->getReflection()->getNamespaceName();
    }

    public function getPath(?string $path = null): string
    {
        $parts = [dirname($this->getReflection()->getFileName())];

        if ($path) {
            $parts[] = ltrim($path, DIRECTORY_SEPARATOR);
        }

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public function getConfig(?string $key = null, mixed $default = null): mixed
    {
        $parts = [
            $this->getName(),
            $key,
        ];
        $key = implode('.', array_filter($parts));

        return $this->app->get('config')->get($key, $default);
    }

    public function mergeConfig(string $key, array $config): void
    {
        $key = sprintf('%s.%s', $this->getName(), $key);

        $this->app->get('config')->set($key, [...$this->getConfig($key, []), ...$config]);
    }

    public function callAfterResolving(string $name, callable $callback): void
    {
        $this->app->afterResolving($name, $callback);

        if ($this->app->resolved($name)) {
            $callback($this->app->make($name), $this->app);
        }
    }

    /**
     * Register a booting callback to be run before the "boot" method is called.
     *
     * @param Closure $callback
     */
    public function booting(Closure $callback): void
    {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a booted callback to be run after the "boot" method is called.
     *
     * @param Closure $callback
     */
    public function booted(Closure $callback): void
    {
        $this->bootedCallbacks[] = $callback;
    }

    /**
     * Call the registered booting callbacks.
     *
     * @return void
     */
    public function callBootingCallbacks(): void
    {
        $index = 0;

        while ($index < count($this->bootingCallbacks)) {
            $this->app->call($this->bootingCallbacks[$index]);

            $index++;
        }
    }

    /**
     * Call the registered booted callbacks.
     *
     * @return void
     */
    public function callBootedCallbacks(): void
    {
        $index = 0;

        while ($index < count($this->bootedCallbacks)) {
            $this->app->call($this->bootedCallbacks[$index]);

            $index++;
        }
    }

    public function when(): array
    {
        return [];
    }

    public function isDeferred(): bool
    {
        return $this instanceof DeferrableProvider;
    }

    public function provides(): array
    {
        return [];
    }

    public function configurePlugin(): void
    {
        foreach ($this->initializedPlugins as $plugin) {
            $plugin->configure();
        }
    }

    public function register(): void
    {
    }

    public function configure(): void
    {
    }

    public function bootPlugin(): void
    {
        foreach ($this->initializedPlugins as $plugin) {
            $plugin->boot();
        }
    }

    public function boot(): void
    {
    }

    protected function transformName(string $name): string
    {
        return Str::snake(preg_replace('/Bundle$/', '', $name));
    }

    protected function getReflection(): ReflectionClass
    {
        if (null === $this->reflection) {
            $this->reflection = new ReflectionClass(static::class);
        }

        return $this->reflection;
    }
}
