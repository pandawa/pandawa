<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SessionBundle;

use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Bundle\RoutingBundle\Plugin\ImportMiddlewareAnnotationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SessionBundle extends Bundle implements HasPluginInterface
{
    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([
                SessionServiceProvider::class,
                CookieServiceProvider::class,
            ]),
            new ImportMiddlewareAnnotationPlugin(),
        ];
    }
}
