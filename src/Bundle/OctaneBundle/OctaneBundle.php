<?php

declare(strict_types=1);

namespace Pandawa\Bundle\OctaneBundle;

use Laravel\Octane\OctaneServiceProvider;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class OctaneBundle extends Bundle implements HasPluginInterface
{
    public function boot(): void
    {
        // Register fake view engine resolver if not exists
        $this->app->booted(function () {
            if (!$this->app->bound('view.engine.resolver')) {
                $this->app->singleton('view.engine.resolver', function () {
                    return new class {
                        public function forget(string $engine): void
                        {
                        }
                    };
                });
            }
        });
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([
                OctaneServiceProvider::class,
            ]),
        ];
    }
}
