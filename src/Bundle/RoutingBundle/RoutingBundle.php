<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle;

use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RoutingBundle extends Bundle
{
    public function register(): void
    {
        $this->booted(function () {
            if ($this->app->routesAreCached()) {
                $this->app->booted(function () {
                    require $this->app->getCachedRoutesPath();
                });
            }
        });
    }

    protected function plugins(): array
    {
        return [
            new ImportServicesPlugin(),
        ];
    }
}
