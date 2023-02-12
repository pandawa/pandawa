<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Component\Foundation\ResourcePublisher;
use Pandawa\Contracts\Config\LoaderInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * This plugin is used to define, load and publish config.
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportConfigurationPlugin extends Plugin
{
    public function __construct(
        protected string $basePath = 'Resources/config',
        protected string $definitionFilename = 'definition',
        protected string $configFilename = 'config',
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
            [
                ...$this->loadConfigs(),
                ...$registry->get($key, [])
            ]
        ));
    }

    public function boot(): void
    {
        if ($this->bundle->getApp()->runningInConsole()) {
            foreach ($this->getBundleConfigs() as $bundleConfig) {
                if (!file_exists($bundleConfig)) {
                    continue;
                }

                $bundleName = $this->bundle->getName();

                ResourcePublisher::publishes(
                    $this->bundle::class,
                    [
                        $bundleConfig => $this->getPackageConfigPath(
                            $bundleName.'.'.pathinfo($bundleConfig, PATHINFO_EXTENSION)
                        ),
                    ],
                    [$bundleName . '-config', $bundleName, 'config']
                );
            }
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
        return $this->bundle->getPath($this->basePath.DIRECTORY_SEPARATOR.$this->definitionFilename.'.php');
    }

    protected function loadConfigs(): array
    {
        $paths = [
            ...$this->getBundleConfigs(),
            ...$this->getPackageConfigs(),
        ];

        $configs = [];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $configs = array_merge($configs, $this->configLoader()->load($path));
            }
        }

        return $configs;
    }

    protected function getBundleConfigs(): array
    {
        return [
            $this->getBundleConfigPath($this->configFilename.'.php'),
            $this->getBundleConfigPath($this->configFilename.'.yaml'),
        ];
    }

    protected function getBundleConfigPath(string $filename): string
    {
        return $this->bundle->getPath($this->basePath.DIRECTORY_SEPARATOR.$filename);
    }

    protected function getPackageConfigs(): array
    {
        return [
            $this->getPackageConfigPath($this->bundle->getName().'.php'),
            $this->getPackageConfigPath($this->bundle->getName().'.yaml'),
        ];
    }

    protected function getPackageConfigPath(string $filename): string
    {
        return $this->bundle->getApp()->configPath($filename);
    }

    protected function configLoader(): LoaderInterface
    {
        return $this->bundle->getApp()->get(LoaderInterface::class);
    }
}
