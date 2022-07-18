<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Configurator;

use Illuminate\Routing\Route;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteParamsConfigurator implements RouteConfiguratorInterface
{
    const OPTION_KEY = 'params';

    public function configure(Route $route, array $options): Route
    {
        if ($params = $options[self::OPTION_KEY] ?? null) {
            $route = $route->where($params);
        }

        return $route;
    }
}
