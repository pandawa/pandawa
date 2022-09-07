<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Plugin;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Router;
use Pandawa\Annotations\Routing\AsMiddleware;
use Pandawa\Bundle\AnnotationBundle\Plugin\ImportAnnotationPlugin;
use Pandawa\Bundle\RoutingBundle\Annotation\MiddlewareLoadHandler;
use Pandawa\Bundle\RoutingBundle\RoutingBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportMiddlewareAnnotationPlugin extends Plugin
{
    public function __construct(
        protected readonly string $directory = 'Http/Middleware',
        protected readonly array $exclude = [],
        protected readonly array $scopes = [],
    ) {
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            $this->loadAliasFromArray(
                $this->config()->get(
                    $this->getAliasConfigKey(),
                    []
                )
            );

            $this->loadGroupFromArray(
                $this->config()->get(
                    $this->getGroupsConfigKey(),
                    []
                )
            );

            return;
        }

        $this->importAnnotations();
    }

    protected function loadAliasFromArray(array $aliases): void
    {
        foreach ($aliases as $name => $middleware) {
            $this->router()->aliasMiddleware($name, $middleware);
        }
    }

    protected function loadGroupFromArray(array $groups): void
    {
        foreach ($groups as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $this->router()->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }

    protected function importAnnotations(): void
    {
        $annotationPlugin = new ImportAnnotationPlugin(
            annotationClasses: [AsMiddleware::class],
            directories: [$this->bundle->getPath($this->directory)],
            classHandler: MiddlewareLoadHandler::class,
            dontRunIfCached: false,
            exclude: $this->exclude,
            scopes: $this->scopes,
        );
        $annotationPlugin->setBundle($this->bundle);
        $annotationPlugin->configure();
    }

    protected function router(): Router
    {
        return $this->bundle->getService('router');
    }

    protected function config(): Repository
    {
        return $this->bundle->getService('config');
    }

    protected function getAliasConfigKey(): string
    {
        return RoutingBundle::MIDDLEWARE_CONFIG_KEY . '.' . $this->bundle->getName() . '.aliases';
    }

    protected function getGroupsConfigKey(): string
    {
        return RoutingBundle::MIDDLEWARE_CONFIG_KEY . '.' . $this->bundle->getName() . '.groups';
    }
}
