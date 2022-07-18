<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Configurator;

use Illuminate\Routing\Route;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteMiddlewareConfigurator implements RouteConfiguratorInterface
{
    const OPTION_KEY = 'middleware';

    public function configure(Route $route, array $options): Route
    {
        if ($middleware = $options[self::OPTION_KEY] ?? null) {
            $route->middleware($middleware);
        }

        return $route;
    }
}
