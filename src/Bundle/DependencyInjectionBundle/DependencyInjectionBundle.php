<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DependencyInjectionBundle;

use Illuminate\Contracts\Container\Container;
use Pandawa\Component\Config\Parser\ArrayParser;
use Pandawa\Component\Config\Parser\ParserResolver;
use Pandawa\Component\DependencyInjection\Factory\ClassServiceFactory;
use Pandawa\Component\DependencyInjection\Factory\ConfigurationFactory;
use Pandawa\Component\DependencyInjection\Factory\FactoryResolver;
use Pandawa\Component\DependencyInjection\Factory\FactoryServiceFactory;
use Pandawa\Component\DependencyInjection\Parser\ServiceParser;
use Pandawa\Component\DependencyInjection\Parser\TagParser;
use Pandawa\Component\DependencyInjection\ServiceRegistry;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Config\ProcessorInterface;
use Pandawa\Contracts\DependencyInjection\Factory\ConfigurationFactoryInterface;
use Pandawa\Contracts\DependencyInjection\Factory\FactoryResolverInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DependencyInjectionBundle extends Bundle
{
    public function register(): void
    {
        $this->registerConfigurationFactory();
        $this->registerFactoryResolver();
        $this->registerServiceParser();
        $this->registerServiceRegistry();
    }

    protected function registerConfigurationFactory(): void
    {
        $this->app->singleton('service.factory.config', ConfigurationFactory::class);
        $this->app->alias('service.factory.config', ConfigurationFactoryInterface::class);
    }

    protected function registerFactoryResolver(): void
    {
        $this->app->singleton('service.factory.class', fn(Container $container) => new ClassServiceFactory(
            $container->get('service.resolver.parser'),
        ));
        $this->app->singleton('service.factory.factory', fn(Container $container) => new FactoryServiceFactory(
            $container,
            $container->get('service.resolver.parser'),
        ));
        $this->app->tag(['service.factory.class', 'service.factory.factory'], 'ServiceFactories');

        $this->app->singleton('service.resolver.factory', fn(Container $container) => new FactoryResolver(
            iterator_to_array($container->tagged('ServiceFactories')->getIterator()),
        ));
        $this->app->alias('service.resolver.factory', FactoryResolverInterface::class);
    }

    protected function registerServiceParser(): void
    {
        $this->app->singleton('service.parser.array', ArrayParser::class);
        $this->app->singleton('service.parser.service', ServiceParser::class);
        $this->app->singleton('service.parser.tag', TagParser::class);
        $this->app->tag(['service.parser.array', 'service.parser.service', 'service.parser.tag'], 'ServiceParsers');

        $this->app->singleton('service.resolver.parser', fn(Container $container) => new ParserResolver(
            iterator_to_array($container->tagged('ServiceParsers')->getIterator()),
        ));
    }

    protected function registerServiceRegistry(): void
    {
        $this->app->singleton('service.registry', fn(Container $container) => new ServiceRegistry(
            $container,
            $container->get('config.resolver.parser'),
            $container->get('service.resolver.factory'),
            $container->get('service.factory.config'),
            $container->get(ProcessorInterface::class)
        ));
        $this->app->alias('service.registry', ServiceRegistryInterface::class);
    }
}
