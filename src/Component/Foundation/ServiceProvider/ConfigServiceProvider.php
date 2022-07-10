<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use Pandawa\Component\Config\Loader\ChainLoader;
use Pandawa\Contracts\Config\LoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoaderInterface::class, fn() => ChainLoader::defaults());
    }
}
