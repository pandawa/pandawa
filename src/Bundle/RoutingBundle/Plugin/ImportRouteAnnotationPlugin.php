<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Plugin;

use Pandawa\Annotations\Routing\Routable;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\RoutingBundle\Annotation\RouteLoadHandler;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportRouteAnnotationPlugin extends AnnotationPlugin
{
    public function configure(): void
    {
        if ($this->bundle->getApp()->routesAreCached()) {
            return;
        }

        $this->importAnnotations();
    }

    protected function getAnnotationClasses(): array
    {
        return [Routable::class];
    }

    protected function getHandler(): string
    {
        return RouteLoadHandler::class;
    }
}
