<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;
use Pandawa\Contracts\Config\ProcessorInterface;
use Pandawa\Contracts\DependencyInjection\Factory\ConfigurationFactoryInterface;
use Pandawa\Contracts\DependencyInjection\Factory\FactoryResolverInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    public function __construct(
        protected Container $container,
        protected ParserResolverInterface $parserResolver,
        protected FactoryResolverInterface $factoryResolver,
        protected ConfigurationFactoryInterface $configurationFactory,
        protected ProcessorInterface $configurationProcessor,
    ) {
    }

    public function load(array $services): void
    {
        foreach ($services as $name => $config) {
            $this->bind($name, $config);
        }
    }

    public function register(string $name, array $config): void
    {
        $configName = $this->normalizeServiceName($name);
        $config = $this->configurationProcessor->process(
            $this->configurationFactory->create($configName)->buildTree(),
            [$configName => $config]
        );

        $this->bind($name, $config);
    }

    protected function bind(string $name, array $config): void
    {
        $name = $this->parse($name);
        $config = $this->parse($config);

        $this->container->bind($name, $this->factory($name, $config), $config['shared'] ?? true);

        if ($tag = $config['tag'] ?? null) {
            $this->container->tag([$name], $tag);
        }

        if ($aliases = $config['alias'] ?? null) {
            foreach ((array)$aliases as $alias) {
                $this->container->alias($name, $alias);
            }
        }
    }

    protected function parse(mixed $value): mixed
    {
        if (null !== $parser = $this->parserResolver->resolve($value)) {
            return $parser->parse($value);
        }

        return $value;
    }

    protected function factory(string $name, array $config): callable|string
    {
        if (null === $factory = $this->factoryResolver->resolve($config)) {
            throw new InvalidArgumentException(sprintf('Unsupported factory service for "%s".', $name));
        }

        return $factory->create($config);
    }

    protected function normalizeServiceName(string $name): string
    {
        return str_replace(['.', '-'], '_', $name);
    }
}
