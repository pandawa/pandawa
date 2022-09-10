<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AuthBundle;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Contracts\Auth\Factory;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Bundle\RoutingBundle\Plugin\ImportMiddlewareAnnotationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthBundle extends Bundle implements HasPluginInterface
{
    const POLICY_CONFIG_KEY = 'pandawa.policies';

    public function configure(): void
    {
        $this->app->alias('auth', Factory::class);
        $this->app->alias('auth', AuthManager::class);
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([
                AuthServiceProvider::class,
            ]),
            new ImportConfigurationPlugin(),
            new ImportMiddlewareAnnotationPlugin(),
        ];
    }
}
