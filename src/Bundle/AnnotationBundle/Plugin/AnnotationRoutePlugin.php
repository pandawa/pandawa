<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AnnotationBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AnnotationRoutePlugin extends Plugin
{
    protected ?string $targetClass = null;

    public function __construct(
        protected readonly array $directories = [],
        protected array $exclude = [],
        protected array $scopes = [],
    ) {
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
            exclude: $this->exclude,
            scopes: $this->scopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    abstract protected function getAnnotationClasses(): array;

    abstract protected function getHandler(): string;

    abstract protected function getDirectories(): array;
}
