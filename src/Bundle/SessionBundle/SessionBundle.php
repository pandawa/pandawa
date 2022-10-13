<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SessionBundle;

use Illuminate\Contracts\Session\Session;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Session\SessionManager;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Session\Store;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Bundle\RoutingBundle\Plugin\ImportMiddlewareAnnotationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SessionBundle extends Bundle implements HasPluginInterface
{
    public function configure(): void
    {
        $this->app->alias('session', SessionManager::class);
        $this->app->alias('session.store', Store::class);
        $this->app->alias('session.store', Session::class);
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([
                SessionServiceProvider::class,
                CookieServiceProvider::class,
            ]),
            new ImportMiddlewareAnnotationPlugin(),
            new ImportConfigurationPlugin(),
        ];
    }
}
