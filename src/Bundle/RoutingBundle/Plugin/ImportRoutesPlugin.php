<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Plugin;

use Illuminate\Routing\Router;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Routing\LoaderResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportRoutesPlugin extends Plugin
{
    public function __construct(
        protected string $basePath = 'Resources/routes',
        protected string $entryRouteFilename = 'routes',
    ) {
    }

    public function configure(): void
    {
        $this->bundle->getApp()->booted(function () {
            if ($this->bundle->getApp()->routesAreCached()) {
                return;
            }

            $this->loadRoutes();

            $this->bundle->getApp()->booted(function () {
                $this->router()->getRoutes()->refreshNameLookups();
                $this->router()->getRoutes()->refreshActionLookups();
            });
        });
    }

    protected function loadRoutes(): void
    {
        foreach (['.php', '.yaml'] as $ext) {
            $filepath = $this->getRoutePath($this->entryRouteFilename.$ext);
            if (file_exists($filepath)) {
                $this->routeResolver()->resolve($filepath)->load($filepath);

                return;
            }
        }
    }

    protected function getRoutePath(string $filename): string
    {
        return $this->bundle->getPath($this->basePath.DIRECTORY_SEPARATOR.$filename);
    }

    protected function routeResolver(): LoaderResolverInterface
    {
        return $this->bundle->getService(LoaderResolverInterface::class);
    }

    protected function router(): Router
    {
        return $this->bundle->getService('router');
    }
}
