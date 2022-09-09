<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AnnotationBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AnnotationPlugin extends Plugin
{
    protected ?string $targetClass = null;

    protected ?string $defaultPath = null;

    public function __construct(
        protected readonly array $directories = [],
        protected array $exclude = [],
        protected array $scopes = [],
    ) {
    }

    public function boot(): void
    {
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $this->importAnnotations();
    }

    protected function importAnnotations(): void
    {
        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: $this->getAnnotationClasses(),
            directories: $this->getDirectories(),
            classHandler: $this->getHandler(),
            targetClass: $this->targetClass,
            dontRunIfCached: false,
            exclude: $this->exclude,
            scopes: $this->scopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function getDirectories(): array
    {
        if (empty($this->directories)) {
            return [$this->bundle->getPath($this->defaultPath ?? '')];
        }

        return array_map(
            fn(string $path) => $this->bundle->getPath($path),
            $this->directories
        );
    }

    abstract protected function getAnnotationClasses(): array;

    abstract protected function getHandler(): string;
}
