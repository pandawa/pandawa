<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

/**
 * @property Application $app
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ConsoleProviderTrait
{
    public function bootConsoleProvider(): void
    {
        $this->commands(
            array_values(
                config(sprintf('pandawa_consoles.%s', $this->getModuleName())) ?? []
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    protected function registerConsoleProvider(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $consolePath = $this->getCurrentPath() . '/Console';
        $key = sprintf('pandawa_consoles.%s', $this->getModuleName());

        if (!is_dir($consolePath)) {
            return;
        }

        foreach (Finder::create()->in($consolePath)->name('*Console.php')->files() as $console) {
            $console = $this->getClassFromFile($console);

            if (is_subclass_of($console, Command::class)
                && !(new ReflectionClass($console))->isAbstract()) {

                $consoles = $this->app['config']->get($key) ?? [];
                $consoles[md5($console)] = $console;

                $this->mergeConfig($key, $consoles);
            }
        }
    }
}
