<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ConsoleBundle\Plugin;

use Illuminate\Console\Command;
use Pandawa\Annotations\Console\AsConsole;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\ConsoleBundle\Annotation\ConsoleLoadHandler;
use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportConsolePlugin extends Plugin
{
    public function __construct(
        protected readonly array $directories = ['Console'],
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
            annotationClasses: [AsConsole::class],
            directories: $this->getDirectories(),
            classHandler: ConsoleLoadHandler::class,
            targetClass: Command::class,
            exclude: $this->exclude,
            scopes: $this->scopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->boot();
    }

    protected function getDirectories(): array
    {
        return array_map(
            function (string $path) {
                return $this->bundle->getPath($path);
            },
            $this->directories
        );
    }
}
