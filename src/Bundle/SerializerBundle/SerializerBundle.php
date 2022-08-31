<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SerializerBundle;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SerializerBundle extends Bundle implements HasPluginInterface, DeferrableProvider
{
    protected array $deferred = [];

    public function configure(): void
    {
        foreach ($this->getConfig('serializers') as $name => $config) {
            $serviceName = sprintf('serializer.%s', $name);

            $this->app->singleton($serviceName, function (Container $container) use ($config) {
                return $container->get('serializer.factory')->create(
                    $config['normalizers'],
                    $config['encoders'],
                );
            });

            $this->deferred[] = $serviceName;
        }

        $this->app->alias(
            sprintf('serializer.%s', $this->getConfig('default')),
            SerializerInterface::class
        );

        $this->app->alias(
            sprintf('serializer.%s', $this->getConfig('default')),
            Serializer::class
        );
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }

    public function provides(): array
    {
        return [
            ...$this->deferred,
            SerializerInterface::class,
            Serializer::class,
        ];
    }
}
