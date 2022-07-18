<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Routing;

use Illuminate\Routing\Route;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RouteConfiguratorInterface
{
    public function configure(Route $route, array $options): Route;
}
