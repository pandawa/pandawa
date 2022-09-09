<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Plugin;

use Illuminate\Routing\Router;
use Pandawa\Annotations\Routing\AsMiddleware;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\RoutingBundle\Annotation\MiddlewareLoadHandler;
use Pandawa\Bundle\RoutingBundle\RoutingBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportMiddlewareAnnotationPlugin extends AnnotationPlugin
{
    protected ?string $defaultPath = 'Http/Middleware';

    public function boot(): void
    {
        $config = $this->bundle->getService('config');

        $this->loadMiddlewareAliasFromArray(
            $config->get(
                $this->getAliasConfigKey(),
                []
            )
        );

        $this->loadMiddlewareGroupFromArray(
            $config->get(
                $this->getGroupsConfigKey(),
                []
            )
        );
    }

    protected function getAnnotationClasses(): array
    {
        return [AsMiddleware::class];
    }

    protected function getHandler(): string
    {
        return MiddlewareLoadHandler::class;
    }

    protected function loadMiddlewareAliasFromArray(array $aliases): void
    {
        foreach ($aliases as $name => $middleware) {
            $this->router()->aliasMiddleware($name, $middleware);
        }
    }

    protected function loadMiddlewareGroupFromArray(array $groups): void
    {
        foreach ($groups as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $this->router()->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }

    protected function router(): Router
    {
        return $this->bundle->getService('router');
    }

    protected function getAliasConfigKey(): string
    {
        return RoutingBundle::MIDDLEWARE_ALIASES_CONFIG_KEY . '.' . $this->bundle->getName() . '.aliases';
    }

    protected function getGroupsConfigKey(): string
    {
        return RoutingBundle::MIDDLEWARE_GROUPS_CONFIG_KEY . '.' . $this->bundle->getName() . '.groups';
    }
}
