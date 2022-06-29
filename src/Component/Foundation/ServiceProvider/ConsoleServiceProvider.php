<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\ServiceProvider;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Providers\ComposerServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConsoleServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
    protected $providers = [
        ArtisanServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
