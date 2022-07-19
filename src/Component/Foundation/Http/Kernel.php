<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Kernel extends HttpKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Pandawa\Component\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Pandawa\Component\Foundation\Bootstrap\ConfigureBundles::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];
}
