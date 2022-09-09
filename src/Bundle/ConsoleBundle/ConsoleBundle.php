<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ConsoleBundle;

use Illuminate\Console\Application as Artisan;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConsoleBundle extends Bundle
{
    const CONSOLE_CONFIG_KEY = 'pandawa.consoles';

    public function boot(): void
    {
        $this->app->booted(function () {
            $this->loadConsoles(
                $this->getConfig()->get(
                    self::CONSOLE_CONFIG_KEY,
                    []
                )
            );
        });
    }

    protected function loadConsoles(array $commands): void
    {
        Artisan::starting(function (Artisan $artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }
}
