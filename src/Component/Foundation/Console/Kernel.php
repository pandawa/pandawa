<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Console;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Pandawa\Contracts\Foundation\ApplicationInterface;

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

    public function __construct(ApplicationInterface $app, Dispatcher $events)
    {
        parent::__construct($app, $events);

        $this->bootstrappers = $app->getFoundationConfig('console.kernel.bootstrappers', $this->bootstrappers);
    }

    public function getApplication(): Application
    {
        return $this->app;
    }
}
