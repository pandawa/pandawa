<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Configurator;

use Illuminate\Routing\Route;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteOptionsConfigurator implements RouteConfiguratorInterface
{
    const OPTION_KEY = 'options';

    public function configure(Route $route, array $options): Route
    {
        if ($options = $options[self::OPTION_KEY] ?? null) {
            $route->defaults = [
                ...($route->defaults ?? []),
                ...$options,
            ];
        }

        return $route;
    }
}
