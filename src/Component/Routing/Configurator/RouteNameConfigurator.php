<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Configurator;

use Illuminate\Routing\Route;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteNameConfigurator implements RouteConfiguratorInterface
{
    const OPTION_KEY = 'name';

    public function configure(Route $route, array $options): Route
    {
        if ($name = $options[self::OPTION_KEY] ?? null) {
            $route->name($name);
        }

        return $route;
    }
}
