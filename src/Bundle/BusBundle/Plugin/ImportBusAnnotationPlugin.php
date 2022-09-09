<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Plugin;

use Pandawa\Annotations\Bus\AsHandler;
use Pandawa\Annotations\Bus\AsMessage;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\BusBundle\Annotation\MessageHandlerLoadHandler;
use Pandawa\Bundle\BusBundle\Annotation\MessageLoadHandler;
use Pandawa\Component\Foundation\Bundle\Plugin;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportBusAnnotationPlugin extends Plugin
{
    public function __construct(
        protected readonly array $messageDirectories = [],
        protected readonly array $handlerDirectories = [],
        protected readonly array $messageExclude = [],
        protected readonly array $messageScopes = [],
        protected readonly array $handlerExclude = [],
        protected readonly array $handlerScopes = [],
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

        $this->importMessageAnnotations();
        $this->importHandlerAnnotations();
    }

    protected function importMessageAnnotations(): void
    {
        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [AsMessage::class],
            directories: $this->getMessageDirectories(),
            classHandler: MessageLoadHandler::class,
            exclude: $this->messageExclude,
            scopes: $this->messageScopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->boot();
    }

    protected function importHandlerAnnotations(): void
    {
        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [AsHandler::class],
            directories: $this->getHandlerDirectories(),
            classHandler: MessageHandlerLoadHandler::class,
            exclude: $this->handlerExclude,
            scopes: $this->handlerScopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->boot();
    }

    protected function getMessageDirectories(): array
    {
        if (empty($this->directories)) {
            return array_filter([
                $this->getPath('Command'),
                $this->getPath('Query'),
            ]);
        }

        return $this->directories;
    }

    protected function getHandlerDirectories(): array
    {
        if (empty($this->directories)) {
            return array_filter(
                [
                    $this->getPath('Handler'),
                    $this->getPath('CommandHandler'),
                    $this->getPath('QueryHandler'),
                ]
            );
        }

        return $this->directories;
    }

    protected function getPath(string $path): ?string
    {
        if (is_dir($targetPath = $this->bundle->getPath($path))) {
            return $targetPath;
        }

        return null;
    }
}
