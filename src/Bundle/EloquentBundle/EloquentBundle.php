<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Database\Eloquent\Model;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EloquentBundle extends Bundle implements HasPluginInterface
{
    const REPOSITORY_CONFIG_KEY = 'eloquent.repositories';

    /**
     * The array of resolved Faker instances.
     *
     * @var array
     */
    protected static array $fakers = [];

    public function boot(): void
    {
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);

        $this->app->booted(function () {
            $this->serviceRegistry()->load(
                $this->app['config']->get(
                    self::REPOSITORY_CONFIG_KEY,
                    []
                )
            );
        });
    }

    public function register(): void
    {
        Model::clearBootedModels();

        $this->registerEloquentFactory();
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }

    protected function registerEloquentFactory(): void
    {
        $this->app->singleton(FakerGenerator::class, function ($app, $parameters) {
            $locale = $parameters['locale'] ?? $app['config']->get('app.faker_locale', 'en_US');

            if (!isset(static::$fakers[$locale])) {
                static::$fakers[$locale] = FakerFactory::create($locale);
            }

            static::$fakers[$locale]->unique(true);

            return static::$fakers[$locale];
        });
    }

    protected function serviceRegistry(): ServiceRegistryInterface
    {
        return $this->getService(ServiceRegistryInterface::class);
    }
}
