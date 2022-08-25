<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AnnotationBundle;

use Illuminate\Contracts\Container\Container;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Annotation\Factory\AnnotationLoaderFactory;
use Pandawa\Component\Annotation\Factory\ReaderFactory;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Annotation\Factory\AnnotationLoaderFactoryInterface;
use Pandawa\Contracts\Annotation\Factory\ReaderFactoryInterface;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AnnotationBundle extends Bundle implements HasPluginInterface
{
    public function register(): void
    {
        $this->registerFactories();
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
        ];
    }

    protected function registerFactories(): void
    {
        $this->app->singleton('annotation.factory.reader', fn(Container $container) => new ReaderFactory(
            $container->get('config')->get('annotation.readers', []),
            $container
        ));

        $this->app->alias('annotation.factory.reader', ReaderFactoryInterface::class);

        $this->app->singleton('annotation.factory.annotation_loader', AnnotationLoaderFactory::class);
        $this->app->alias('annotation.factory.annotation_loader', AnnotationLoaderFactoryInterface::class);
    }
}
