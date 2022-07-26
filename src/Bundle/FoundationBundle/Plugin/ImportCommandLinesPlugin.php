<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle\Plugin;

use Generator;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Pandawa\Component\Foundation\Bundle\Plugin;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportCommandLinesPlugin extends Plugin
{
    const CACHE_KEY = 'pandawa.consoles';

    public function __construct(
        protected string $scanPath = 'Console',
        protected string $names = '*Console.php',
    ) {
    }

    public function boot(): void
    {
        $this->bundle->getApp()->loadConsoles(
            array_values(
                $this->config()->get($this->getConfigKey(), [])
            )
        );
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        if (!is_dir($this->getConsolePath())) {
            return;
        }

        $this->registerConsoles();
    }

    protected function registerConsoles(): void
    {
        $consoles = [];
        foreach ($this->getConsoleClasses() as $class) {
            $consoles[md5($class)] = $class;
        }

        $this->config()->set(
            $configKey = $this->getConfigKey(),
            [
                ...$this->config()->get($configKey, []),
                ...$consoles,
            ]
        );
    }

    protected function getConfigKey(): string
    {
        return static::CACHE_KEY.'.'.$this->bundle->getName();
    }

    protected function config(): Repository
    {
        return $this->bundle->getService('config');
    }

    protected function getConsoleClasses(): Generator
    {
        foreach (Finder::create()->in($this->getConsolePath())->name($this->names)->files() as $consoleFile) {
            $consoleClass = $this->getClassNameFromFile($consoleFile);

            if (!$this->isCommand($consoleClass)) {
                continue;
            }

            yield $consoleClass;
        }
    }

    protected function isCommand(string $class): bool
    {
        return is_subclass_of($class, Command::class)
            && !(new ReflectionClass($class))->isAbstract();
    }

    protected function getClassNameFromFile(SplFileInfo $file): string
    {
        $className = sprintf(
            '%s\\%s',
            $this->bundle->getNamespace(),
            str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getPathname(), $this->bundle->getPath().'/')
            )
        );

        return preg_replace('/\\+/', '\\', $className);
    }

    protected function getConsolePath(): string
    {
        return $this->bundle->getPath($this->scanPath);
    }
}
