<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Plugin;

use Pandawa\Annotations\Eloquent\AsRepository;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\EloquentBundle\Annotation\RepositoryLoadHandler;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Eloquent\RepositoryInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportRepositoryAnnotationPlugin extends Plugin
{
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

    public function boot(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $this->importAnnotations();
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
        $annotationPlugin->boot();
    }

    protected function getDirectories(): array
    {
        if (empty($this->directories)) {
            return [$this->bundle->getPath('Repository')];
        }

        return $this->directories;
    }
}
