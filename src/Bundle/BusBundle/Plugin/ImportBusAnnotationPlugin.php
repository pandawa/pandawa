<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Plugin;

use Pandawa\Annotations\Bus\AsHandler;
use Pandawa\Annotations\Bus\AsMessage;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\BusBundle\Annotation\MessageHandlerLoadHandler;
use Pandawa\Bundle\BusBundle\Annotation\MessageLoadHandler;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
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
        // Load messages
        $this->registry()->load(
            $this->bundle->getService('config')->get(
                $this->getMessageConfigKey(),
                []
            )
        );

        // Load handlers
        $this->bus()->map(
            $this->bundle->getService('config')->get(
                $this->getHandlerConfigKey(),
                []
            )
        );
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
        if (!count($directories = $this->getMessageDirectories())) {
            return;
        }

        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [AsMessage::class],
            directories: $directories,
            classHandler: MessageLoadHandler::class,
            exclude: $this->messageExclude,
            scopes: $this->messageScopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function importHandlerAnnotations(): void
    {
        if (!count($directories = $this->getHandlerDirectories())) {
            return;
        }

        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [AsHandler::class],
            directories: $directories,
            classHandler: MessageHandlerLoadHandler::class,
            exclude: $this->handlerExclude,
            scopes: $this->handlerScopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function getMessageDirectories(): array
    {
        if (empty($this->directories)) {
            return array_filter([
                $this->getPath('Command'),
                $this->getPath('Query'),
                $this->getPath('Event'),
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

    protected function registry(): RegistryInterface
    {
        return $this->bundle->getService(RegistryInterface::class);
    }

    protected function bus(): BusInterface
    {
        return $this->bundle->getService(BusInterface::class);
    }

    protected function getMessageConfigKey(): string
    {
        return BusBundle::PANDAWA_MESSAGE_CONFIG_KEY . '.' . $this->bundle->getName() . '.annotations';
    }

    protected function getHandlerConfigKey(): string
    {
        return BusBundle::PANDAWA_HANDLER_CONFIG_KEY . '.' . $this->bundle->getName() . '.annotations';
    }
}
