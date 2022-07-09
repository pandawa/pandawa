<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Bundle;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Contracts\Foundation\BundleInterface as BundleContract;
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
    private ?ReflectionClass $reflection;

    /**
     * @var PluginInterface[]
     */
    private array $initializedPlugins = [];

    public function __construct(protected Application $app)
    {
        foreach ([...config('app.default_plugins', []), ...$this->plugins()] as $plugin) {
            $this->initializedPlugins[] = tap(
                $plugin,
                fn(PluginInterface $plugin) => $plugin->setBundle($this)
            );
        }
    }

    /**
     * Get the plugins provided by the bundle.
     *
     * @return PluginInterface[]
     */
    protected function plugins(): array
    {
        return [];
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function getName(): string
    {
        return $this->transformName($this->getReflection()->getName());
    }

    protected function transformName(string $name): string
    {
        return preg_replace('/Bundle$/', '', $name);
    }

    protected function getReflection(): ReflectionClass
    {
        if (null === $this->reflection) {
            $this->reflection = new ReflectionClass(static::class);
        }

        return $this->reflection;
    }

    public function getShortName(): string
    {
        return $this->transformName($this->getReflection()->getShortName());
    }

    public function getNamespace(): string
    {
        return $this->getReflection()->getNamespaceName();
    }

    public function getPath(): string
    {
        return dirname($this->getReflection()->getFileName());
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

    public function configurePlugin(): void
    {
        foreach ($this->initializedPlugins as $plugin) {
            $plugin->configure();
        }
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
}
