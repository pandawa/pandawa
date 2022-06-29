<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\ServiceProvider;

use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Cache\Console\ClearCommand as CacheClearCommand;
use Illuminate\Cache\Console\ForgetCommand as CacheForgetCommand;
use Illuminate\Console\Scheduling\ScheduleClearCacheCommand;
use Illuminate\Console\Scheduling\ScheduleFinishCommand;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Console\Scheduling\ScheduleTestCommand;
use Illuminate\Console\Scheduling\ScheduleWorkCommand;
use Illuminate\Foundation\Console\ChannelMakeCommand;
use Illuminate\Foundation\Console\ClearCompiledCommand;
use Illuminate\Foundation\Console\ConfigCacheCommand;
use Illuminate\Foundation\Console\ConfigClearCommand;
use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\EnvironmentCommand;
use Illuminate\Foundation\Console\EventCacheCommand;
use Illuminate\Foundation\Console\EventClearCommand;
use Illuminate\Foundation\Console\EventGenerateCommand;
use Illuminate\Foundation\Console\EventListCommand;
use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Foundation\Console\ExceptionMakeCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Console\ListenerMakeCommand;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Foundation\Console\OptimizeCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand;
use Illuminate\Foundation\Console\ProviderMakeCommand;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Foundation\Console\RouteClearCommand;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Foundation\Console\StorageLinkCommand;
use Illuminate\Foundation\Console\StubPublishCommand;
use Illuminate\Foundation\Console\TestMakeCommand;
use Illuminate\Foundation\Console\UpCommand;
use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as LaravelArtisanServiceProvider;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ArtisanServiceProvider extends LaravelArtisanServiceProvider
{
    protected $commands = [
        'CacheClear'         => CacheClearCommand::class,
        'CacheForget'        => CacheForgetCommand::class,
        'ClearCompiled'      => ClearCompiledCommand::class,
        'ConfigCache'        => ConfigCacheCommand::class,
        'ConfigClear'        => ConfigClearCommand::class,
        'Down'               => DownCommand::class,
        'Environment'        => EnvironmentCommand::class,
        'EventCache'         => EventCacheCommand::class,
        'EventClear'         => EventClearCommand::class,
        'EventList'          => EventListCommand::class,
        'KeyGenerate'        => KeyGenerateCommand::class,
        'Optimize'           => OptimizeCommand::class,
        'OptimizeClear'      => OptimizeClearCommand::class,
        'PackageDiscover'    => PackageDiscoverCommand::class,
        'RouteCache'         => RouteCacheCommand::class,
        'RouteClear'         => RouteClearCommand::class,
        'RouteList'          => RouteListCommand::class,
        'ScheduleFinish'     => ScheduleFinishCommand::class,
        'ScheduleList'       => ScheduleListCommand::class,
        'ScheduleRun'        => ScheduleRunCommand::class,
        'ScheduleClearCache' => ScheduleClearCacheCommand::class,
        'ScheduleTest'       => ScheduleTestCommand::class,
        'ScheduleWork'       => ScheduleWorkCommand::class,
        'StorageLink'        => StorageLinkCommand::class,
        'Up'                 => UpCommand::class,
    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        'CacheTable'     => CacheTableCommand::class,
        'ChannelMake'    => ChannelMakeCommand::class,
        'ConsoleMake'    => ConsoleMakeCommand::class,
        'ControllerMake' => ControllerMakeCommand::class,
        'EventGenerate'  => EventGenerateCommand::class,
        'EventMake'      => EventMakeCommand::class,
        'ExceptionMake'  => ExceptionMakeCommand::class,
        'ListenerMake'   => ListenerMakeCommand::class,
        'MiddlewareMake' => MiddlewareMakeCommand::class,
        'ProviderMake'   => ProviderMakeCommand::class,
        'RequestMake'    => RequestMakeCommand::class,
        'Serve'          => ServeCommand::class,
        'StubPublish'    => StubPublishCommand::class,
        'TestMake'       => TestMakeCommand::class,
        'VendorPublish'  => VendorPublishCommand::class,
    ];
}
