<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Configurator;

use Illuminate\Routing\Route;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ChainRouteConfigurator implements RouteConfiguratorInterface
{
    /**
     * @var RouteConfiguratorInterface[]
     */
    protected array $configurators = [];

    /**
     * @var RouteConfiguratorInterface[] $configurators
     */
    public function __construct(iterable $configurators)
    {
        foreach ($configurators as $configurator) {
            $this->addConfigurator($configurator);
        }
    }

    public function addConfigurator(RouteConfiguratorInterface $configurator): void
    {
        $this->configurators[] = $configurator;
    }

    public function configure(Route $route, array $options): Route
    {
        foreach ($this->configurators as $configurator) {
            $route = $configurator->configure($route, $options);
        }

        return $route;
    }
}
