<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Pandawa\Contracts\Foundation\ApplicationInterface;

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

    public function __construct(ApplicationInterface $app, Router $router)
    {
        parent::__construct($app, $router);

        $this->bootstrappers = $app->getFoundationConfig('http.kernel.bootstrappers', $this->bootstrappers);
        $this->middleware = $app->getFoundationConfig('http.middleware.all', $this->middleware);
        $this->middlewareGroups = $app->getFoundationConfig('http.middleware.groups', $this->middlewareGroups);
        $this->routeMiddleware = $app->getFoundationConfig('http.middleware.route', $this->routeMiddleware);
        $this->middlewarePriority = $app->getFoundationConfig('http.middleware.priorities', $this->middlewarePriority);
    }
}
