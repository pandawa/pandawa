<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DependencyInjectionBundle;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Component\DependencyInjection\Factory\ClassServiceFactory;
use Pandawa\Component\DependencyInjection\Factory\ConfigurationFactory;
use Pandawa\Component\DependencyInjection\Factory\FactoryResolver;
use Pandawa\Component\DependencyInjection\Factory\FactoryServiceFactory;
use Pandawa\Component\DependencyInjection\ServiceRegistry;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\DependencyInjection\Factory\ConfigurationFactoryInterface;
use Pandawa\Contracts\DependencyInjection\Factory\FactoryResolverInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DependencyInjectionBundle extends Bundle implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(ConfigurationFactoryInterface::class, ConfigurationFactory::class);
        $this->app->singleton(FactoryResolverInterface::class, fn(Container $app) => new FactoryResolver([
            $app->make(ClassServiceFactory::class),
            $app->make(FactoryServiceFactory::class),
        ]));
        $this->app->singleton(ServiceRegistryInterface::class, ServiceRegistry::class);
    }

    public function provides(): array
    {
        return [
            ConfigurationFactoryInterface::class,
            FactoryResolverInterface::class,
            ServiceRegistryInterface::class,
        ];
    }
}
