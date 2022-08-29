<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Plugin;

use Pandawa\Annotations\Bus\Handler;
use Pandawa\Annotations\Bus\Message;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
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

    public function configure(): void
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
            annotationClasses: [Message::class],
            directories: $this->getMessageDirectories(),
            classHandler: $this->bundle->getService('config')->get('bus.annotation.message_handler'),
            exclude: $this->messageExclude,
            scopes: $this->messageScopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function importHandlerAnnotations(): void
    {
        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [Handler::class],
            directories: $this->getHandlerDirectories(),
            classHandler: $this->bundle->getService('config')->get('bus.annotation.message_handler_handler'),
            exclude: $this->handlerExclude,
            scopes: $this->handlerScopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function getMessageDirectories(): array
    {
        if (empty($this->directories)) {
            return [
                $this->bundle->getPath('Command'),
                $this->bundle->getPath('Query'),
            ];
        }

        return $this->directories;
    }

    protected function getHandlerDirectories(): array
    {
        if (empty($this->directories)) {
            return [
                $this->bundle->getPath('Handler'),
                $this->bundle->getPath('CommandHandler'),
                $this->bundle->getPath('QueryHandler'),
            ];
        }

        return $this->directories;
    }
}
