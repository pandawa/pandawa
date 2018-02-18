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
use Illuminate\Console\Application as Artisan;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ConsoleProviderTrait
{
    /**
     * @throws ReflectionException
     */
    protected function bootConsoleProvider(): void
    {
        $consolePath = $this->getCurrentPath() . '/Console';

        if (!is_dir($consolePath)) {
            return;
        }

        foreach (Finder::create()->in($consolePath)->name('*Console.php')->files() as $console) {
            $console = $this->getClassFromFile($console);

            if (is_subclass_of($console, Command::class)
                && !(new ReflectionClass($console))->isAbstract()) {
                Artisan::starting(
                    function ($artisan) use ($console) {
                        $artisan->resolve($console);
                    }
                );
            }
        }
    }
}
