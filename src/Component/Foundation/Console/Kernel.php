<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Console;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Kernel extends ConsoleKernel
{
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Pandawa\Component\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Pandawa\Component\Foundation\Bootstrap\ConfigureBundles::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    public function getApplication(): Application
    {
        return $this->app;
    }
}
