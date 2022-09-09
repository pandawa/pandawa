<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ConsoleBundle\Plugin;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Pandawa\Annotations\Console\AsConsole;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\ConsoleBundle\Annotation\ConsoleLoadHandler;
use Pandawa\Bundle\ConsoleBundle\ConsoleBundle;
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
        $this->loadConsoles(
            $this->bundle->getService('config')->get(
                $this->getConfigKey(),
                []
            )
        );
    }

    public function configure(): void
    {
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
        $annotationPlugin->configure();
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

    protected function loadConsoles(array $commands): void
    {
        Artisan::starting(function (Artisan $artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    protected function getConfigKey(): string
    {
        return ConsoleBundle::CONSOLE_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
