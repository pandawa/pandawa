<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Collection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Application extends LaravelApplication
{
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

    public function registerConfiguredProviders(): void
    {
        $providers = Collection::make([
            ...$this->make('config')->get('app.providers'),
            ...$this->getBundles(),
        ])
            ->partition(function ($provider) {
                return str_starts_with($provider, 'Illuminate\\') || str_starts_with($provider, 'Pandawa\\');
            });

        $providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);

        (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
            ->load($providers->collapse()->toArray());
    }

    protected function getBundles(): array
    {
        return Collection::make($this['config']->get('bundles'))
            ->filter(fn(array $item) => $item['all'] ?? null === true || $item[$this->environment()] ?? null === true)
            ->keys()
            ->toArray();
    }

    public function registerCoreContainerAliases(): void
    {
        foreach ($this->coreAliases as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }
}
