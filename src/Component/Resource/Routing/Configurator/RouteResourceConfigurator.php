<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Configurator;

use Illuminate\Routing\Route;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteResourceConfigurator implements RouteConfiguratorInterface
{
    public function configure(Route $route, array $options): Route
    {
        if ($resource = $options['resource'] ?? null) {
            $route->defaults = [
                ...($route->defaults ?? []),
                'resource' => $resource,
            ];
        }

        return $route;
    }
}
