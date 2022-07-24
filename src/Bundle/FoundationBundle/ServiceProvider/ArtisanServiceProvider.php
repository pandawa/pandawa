<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle\ServiceProvider;

use Illuminate\Foundation\Console\ClearCompiledCommand;
use Illuminate\Foundation\Console\ConfigCacheCommand;
use Illuminate\Foundation\Console\ConfigClearCommand;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\EnvironmentCommand;
use Illuminate\Foundation\Console\EventCacheCommand;
use Illuminate\Foundation\Console\EventClearCommand;
use Illuminate\Foundation\Console\EventListCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Foundation\Console\OptimizeCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Foundation\Console\StorageLinkCommand;
use Illuminate\Foundation\Console\StubPublishCommand;
use Illuminate\Foundation\Console\UpCommand;
use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as LaravelArtisanServiceProvider;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ArtisanServiceProvider extends LaravelArtisanServiceProvider
{
    protected $commands = [
        'ClearCompiled'   => ClearCompiledCommand::class,
        'ConfigCache'     => ConfigCacheCommand::class,
        'ConfigClear'     => ConfigClearCommand::class,
        'Down'            => DownCommand::class,
        'Environment'     => EnvironmentCommand::class,
        'EventCache'      => EventCacheCommand::class,
        'EventClear'      => EventClearCommand::class,
        'EventList'       => EventListCommand::class,
        'KeyGenerate'     => KeyGenerateCommand::class,
        'Optimize'        => OptimizeCommand::class,
        'OptimizeClear'   => OptimizeClearCommand::class,
        'PackageDiscover' => PackageDiscoverCommand::class,
        'StorageLink'     => StorageLinkCommand::class,
        'Up'              => UpCommand::class,
    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        'StubPublish'   => StubPublishCommand::class,
        'Serve'         => ServeCommand::class,
        'VendorPublish' => VendorPublishCommand::class,
    ];
}
