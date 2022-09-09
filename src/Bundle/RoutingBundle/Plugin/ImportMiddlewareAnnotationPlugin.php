<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Plugin;

use Pandawa\Annotations\Routing\AsMiddleware;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\RoutingBundle\Annotation\MiddlewareLoadHandler;
use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportMiddlewareAnnotationPlugin extends Plugin
{
    public function __construct(
        protected readonly string $directory = 'Http/Middleware',
        protected readonly array $exclude = [],
        protected readonly array $scopes = [],
    ) {
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
            annotationClasses: [AsMiddleware::class],
            directories: [$this->bundle->getPath($this->directory)],
            classHandler: MiddlewareLoadHandler::class,
            dontRunIfCached: false,
            exclude: $this->exclude,
            scopes: $this->scopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->boot();
    }
}
