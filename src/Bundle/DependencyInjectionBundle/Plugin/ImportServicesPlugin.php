<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DependencyInjectionBundle\Plugin;

use Generator;
use Illuminate\Contracts\Config\Repository;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportServicesPlugin extends Plugin
{
    public function __construct(protected string $scanPath = 'Resources/services')
    {
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            $this->loadFromConfig();

            return;
        }

        $services = [];
        foreach ($this->getServiceFiles() as $file) {
            $services = [
                ...$services,
                ...$this->import($file),
            ];
        }

        $this->mergeConfig($this->getConfigKey(), $services);
        $this->registerDeferServices(array_keys($services));
    }

    protected function mergeConfig(string $key, array $configs): void
    {
        $existing = $this->config()->get($key, []);

        $this->config()->set($key, [...$existing, ...$configs]);
    }

    protected function import(SplFileInfo $file): array
    {
        $services = [];
        foreach ($this->configLoader()->load($file->getRealPath()) as $name => $service) {
            $this->serviceRegistry()->register($name, $service);
            $services[$name] = $service;
        }

        return $services;
    }

    protected function loadFromConfig(): void
    {
        $this->serviceRegistry()->load($services = $this->config()->get($this->getConfigKey(), []));
        $this->registerDeferServices(array_keys($services));
    }

    protected function registerDeferServices(array $serviceKeys): void
    {
        if ($this->bundle->isDeferred()) {
            $deferred = [];
            foreach ($serviceKeys as $key) {
                $deferred[$key] = get_class($this->bundle);
            }

            $this->bundle->getApp()->addDeferredServices($deferred);
        }
    }

    protected function getConfigKey(): string
    {
        return DependencyInjectionBundle::CONFIG_CACHE_KEY.'.'.$this->bundle->getName();
    }

    protected function getServiceFiles(): Generator
    {
        $scanPath = $this->bundle->getPath($this->scanPath);

        if (is_dir($scanPath)) {
            foreach (Finder::create()->name(['*.php', '*.yaml'])->in($scanPath)->files() as $file) {
                yield $file;
            }
        }
    }

    protected function serviceRegistry(): ServiceRegistryInterface
    {
        return $this->bundle->getService(ServiceRegistryInterface::class);
    }

    protected function configLoader(): LoaderInterface
    {
        return $this->bundle->getService(LoaderInterface::class);
    }

    protected function config(): Repository
    {
        return $this->bundle->getService('config');
    }
}
