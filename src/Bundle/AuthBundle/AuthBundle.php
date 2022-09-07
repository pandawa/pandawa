<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AuthBundle;

use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Bundle\RoutingBundle\Plugin\ImportMiddlewareAnnotationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthBundle extends Bundle implements HasPluginInterface
{
    const POLICY_CONFIG_KEY = 'auth.policies';

    public function register(): void
    {
        $this->app->booted(function () {
            foreach ($this->app['config']->get(static::POLICY_CONFIG_KEY) as $model => $policy) {
                $this->gate()->policy($model, $policy);
            }
        });
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([
                AuthServiceProvider::class,
            ]),
            new ImportMiddlewareAnnotationPlugin(),
        ];
    }

    protected function gate(): Gate
    {
        return $this->app[Gate::class];
    }
}
