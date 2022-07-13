<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle\Plugin;

use Illuminate\Support\ServiceProvider;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Config\LoaderInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * This plugin is used to define, load and publish config.
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class LoadConfigurationPlugin extends Plugin
{
    const PACKAGE_CONFIG_PATH = 'packages';

    public function __construct(
        protected string $basePath = 'Resources/config',
        protected string $definitionFilename = 'definition.php',
        protected string $configFilename = 'config.php'
    ) {
    }

    /**
     * Load and register configs.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function configure(): void
    {
        if ($this->isConfigCached()) {
            return;
        }

        $registry = $this->bundle->getApp()->make('config');
        $key = $this->bundle->getName();

        $registry->set($key, $this->processConfiguration(
            $this->loadConfigs()
        ));
    }

    public function boot(): void
    {
        if ($this->bundle->getApp()->runningInConsole()) {
            if (!file_exists($this->getBundleConfigPath())) {
                return;
            }

            $this->publishes([
                $this->getBundleConfigPath() => $this->getPackageConfigPath(),
            ], 'config');
        }
    }

    protected function isConfigCached(): bool
    {
        return $this->bundle->getApp()->configurationIsCached();
    }

    protected function processConfiguration(array $configs): array
    {
        if (null !== $definition = $this->loadConfigDefinition()) {
            $key = $this->bundle->getName();
            $processor = new Processor();
            $configs = $processor->process($definition->buildTree(), [$key => $configs]);
        }

        return $configs;
    }

    protected function loadConfigDefinition(): ?TreeBuilder
    {
        if (file_exists($definitionFile = $this->getBundleConfigDefinitionPath())) {
            $definition = require $definitionFile;
            $treeBuilder = new TreeBuilder($this->bundle->getName());

            return tap($treeBuilder, fn() => $definition($treeBuilder));
        }

        return null;
    }

    protected function getBundleConfigDefinitionPath(): string
    {
        return $this->bundle->getPath($this->basePath.DIRECTORY_SEPARATOR.$this->definitionFilename);
    }

    protected function loadConfigs(): array
    {
        $paths = [
            $this->getBundleConfigPath(),
            $this->getPackageConfigPath(),
        ];

        $configs = [];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $configs = array_merge($configs, $this->configLoader()->load($path));
            }
        }

        return $configs;
    }

    protected function getBundleConfigPath(): string
    {
        return $this->bundle->getPath($this->basePath.DIRECTORY_SEPARATOR.$this->configFilename);
    }

    protected function getPackageConfigPath(): string
    {
        return $this->bundle->getApp()->configPath(
            static::PACKAGE_CONFIG_PATH.DIRECTORY_SEPARATOR.$this->bundle->getName().'.php'
        );
    }

    protected function configLoader(): LoaderInterface
    {
        return $this->bundle->getApp()->get(LoaderInterface::class);
    }

    /**
     * Register paths to be published by the publish command.
     */
    protected function publishes(array $paths, string $groups = null): void
    {
        $this->ensurePublishArrayInitialized($class = $this->bundle::class);

        ServiceProvider::$publishes[$class] = array_merge(ServiceProvider::$publishes[$class], $paths);

        foreach ((array)$groups as $group) {
            $this->addPublishGroup($group, $paths);
        }
    }

    /**
     * Ensure the publish array for the service provider is initialized.
     */
    protected function ensurePublishArrayInitialized(string $class): void
    {
        if (!array_key_exists($class, ServiceProvider::$publishes)) {
            ServiceProvider::$publishes[$class] = [];
        }
    }

    /**
     * Add a publish group / tag to the service provider.
     */
    protected function addPublishGroup(string $group, array $paths): void
    {
        if (!array_key_exists($group, ServiceProvider::$publishGroups)) {
            ServiceProvider::$publishGroups[$group] = [];
        }

        ServiceProvider::$publishGroups[$group] = array_merge(
            ServiceProvider::$publishGroups[$group], $paths
        );
    }
}
