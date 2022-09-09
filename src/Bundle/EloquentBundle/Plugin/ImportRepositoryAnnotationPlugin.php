<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Plugin;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Eloquent\AsRepository;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\EloquentBundle\Annotation\RepositoryLoadHandler;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use Pandawa\Contracts\Eloquent\RepositoryInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportRepositoryAnnotationPlugin extends Plugin
{
    const CACHE_KEY = 'pandawa.repositories';

    public function __construct(
        protected readonly array $directories = [],
        protected readonly array $exclude = [],
        protected readonly array $scopes = [],
    ) {
        if (!class_exists(ImportAnnotationPlugin::class)) {
            throw new RuntimeException(sprintf(
                'Please install package "pandawa/annotation-bundle" to use plugin "%s".',
                (new \ReflectionClass(static::class))->getShortName()
            ));
        }
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            $this->loadFromConfig();

            return;
        }

        $this->importAnnotations();
    }

    protected function loadFromConfig(): void
    {
        $this->serviceRegistry()->load(
            $this->config()->get($this->getConfigKey())
        );
    }

    protected function importAnnotations(): void
    {
        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [AsRepository::class],
            directories: $this->getDirectories(),
            classHandler: RepositoryLoadHandler::class,
            targetClass: RepositoryInterface::class,
            exclude: $this->exclude,
            scopes: $this->scopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function getDirectories(): array
    {
        if (empty($this->directories)) {
            return [$this->bundle->getPath('Repository')];
        }

        return $this->directories;
    }

    protected function serviceRegistry(): ServiceRegistryInterface
    {
        return $this->bundle->getService(ServiceRegistryInterface::class);
    }

    protected function config(): Config
    {
        return $this->bundle->getService('config');
    }

    protected function getConfigKey(): string
    {
        return self::CACHE_KEY . '.' . $this->bundle->getName();
    }
}
