<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation;

use Illuminate\Console\Application as Artisan;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Pandawa\Component\Foundation\ServiceProvider\ConfigServiceProvider;
use Pandawa\Contracts\Foundation\BundleInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Application extends LaravelApplication
{
    const VERSION = '5.0.0';

    protected array $coreAliases = [
        'app'              => [
            self::class,
            \Illuminate\Contracts\Container\Container::class,
            \Illuminate\Contracts\Foundation\Application::class,
            \Psr\Container\ContainerInterface::class,
        ],
        'cache'            => [\Illuminate\Cache\CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
        'cache.store'      => [
            \Illuminate\Cache\Repository::class,
            \Illuminate\Contracts\Cache\Repository::class,
            \Psr\SimpleCache\CacheInterface::class,
        ],
        'cache.psr6'       => [
            \Symfony\Component\Cache\Adapter\Psr16Adapter::class,
            \Symfony\Component\Cache\Adapter\AdapterInterface::class,
            \Psr\Cache\CacheItemPoolInterface::class,
        ],
        'config'           => [\Illuminate\Config\Repository::class, \Illuminate\Contracts\Config\Repository::class],
        'encrypter'        => [
            \Illuminate\Encryption\Encrypter::class,
            \Illuminate\Contracts\Encryption\Encrypter::class,
            \Illuminate\Contracts\Encryption\StringEncrypter::class,
        ],
        'events'           => [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
        'files'            => [\Illuminate\Filesystem\Filesystem::class],
        'filesystem'       => [
            \Illuminate\Filesystem\FilesystemManager::class,
            \Illuminate\Contracts\Filesystem\Factory::class,
        ],
        'filesystem.disk'  => [\Illuminate\Contracts\Filesystem\Filesystem::class],
        'filesystem.cloud' => [\Illuminate\Contracts\Filesystem\Cloud::class],
        'hash'             => [\Illuminate\Hashing\HashManager::class],
        'hash.driver'      => [\Illuminate\Contracts\Hashing\Hasher::class],
        'log'              => [\Illuminate\Log\LogManager::class, \Psr\Log\LoggerInterface::class],
        'redirect'         => [\Illuminate\Routing\Redirector::class],
        'request'          => [\Illuminate\Http\Request::class, \Symfony\Component\HttpFoundation\Request::class],
        'router'           => [
            \Illuminate\Routing\Router::class,
            \Illuminate\Contracts\Routing\Registrar::class,
            \Illuminate\Contracts\Routing\BindingRegistrar::class,
        ],
        'url'              => [
            \Illuminate\Routing\UrlGenerator::class,
            \Illuminate\Contracts\Routing\UrlGenerator::class,
        ],
    ];

    protected bool $configured = false;

    public function registerConfiguredProviders(): void
    {
        $providers = Collection::make([
            ...$this->make('config')->get('app.providers', []),
            ...$this->getBundles(),
        ])
            ->partition(function ($provider) {
                return str_starts_with($provider, 'Illuminate\\') || str_starts_with($provider, 'Pandawa\\');
            });

        $providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);

        (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
            ->load($providers->collapse()->toArray());
    }

    public function registerCoreContainerAliases(): void
    {
        foreach ($this->coreAliases as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    public function register($provider, $force = false): BundleInterface|ServiceProvider
    {
        if (($registered = $this->getProvider($provider)) && !$force) {
            return $registered;
        }

        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();

        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        if ($this->isConfigured() && $provider instanceof BundleInterface) {
            $this->configureBundle($provider);
        }

        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    public function configure(): void
    {
        if ($this->isConfigured()) {
            return;
        }

        array_walk($this->serviceProviders, function ($p) {
            if ($p instanceof BundleInterface) {
                $this->configureBundle($p);
            }
        });

        $this->configured = true;
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function loadConsoles(array $consoles): void
    {
        Artisan::starting(function ($artisan) use ($consoles) {
            $artisan->resolveCommands($consoles);
        });
    }

    protected function registerBaseServiceProviders(): void
    {
        foreach ($this->getBaseServiceProviders() as $provider) {
            $this->register($provider);
        }
    }

    protected function configureBundle(BundleInterface $bundle): void
    {
        $bundle->configurePlugin();
        $bundle->configure();
    }

    protected function bootProvider(ServiceProvider|BundleInterface $provider)
    {
        $provider->callBootingCallbacks();

        if ($provider instanceof BundleInterface) {
            $provider->bootPlugin();
        }

        if (method_exists($provider, 'boot')) {
            $this->call([$provider, 'boot']);
        }

        $provider->callBootedCallbacks();
    }

    protected function getBundles(): array
    {
        $bundles = [];
        foreach ($this['config']->get('bundles', []) as $bundle => $stage) {
            if (is_string($stage)) {
                $bundles[] = $stage;

                continue;
            }

            if (is_array($stage)) {
                if ($stage['all'] ?? null === true || $stage[$this->environment()] ?? null === true) {
                    $bundles[] = $bundle;
                }
            }
        }

        return $bundles;
    }

    protected function getBaseServiceProviders(): array
    {
        return [
            new EventServiceProvider($this),
            new LogServiceProvider($this),
            new RoutingServiceProvider($this),
            new ConfigServiceProvider($this),
        ];
    }
}
