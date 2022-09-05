<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ConsoleBundle\Plugin;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\ConsoleBundle\Annotation\ConsoleLoadHandler;
use Pandawa\Bundle\ConsoleBundle\ConsoleBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Symfony\Component\Console\Attribute\AsCommand;

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
            $this->getConfig()->get($this->getConsoleConfigKey(), [])
        );
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
            annotationClasses: [AsCommand::class],
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

    protected function getConsoleConfigKey(): string
    {
        return ConsoleBundle::CONSOLE_CONFIG_KEY . '.' . $this->bundle->getName();
    }

    protected function getConfig(): Config
    {
        return $this->bundle->getService('config');
    }
}
