<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SchedulingBundle;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Scheduling\ScheduleClearCacheCommand;
use Illuminate\Console\Scheduling\ScheduleFinishCommand;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Console\Scheduling\ScheduleTestCommand;
use Illuminate\Console\Scheduling\ScheduleWorkCommand;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SchedulingBundle extends Bundle
{
    const SCHEDULER_CONFIG_KEY = 'pandawa.schedulers';

    protected array $commands = [
        'ScheduleFinish'     => ScheduleFinishCommand::class,
        'ScheduleList'       => ScheduleListCommand::class,
        'ScheduleRun'        => ScheduleRunCommand::class,
        'ScheduleClearCache' => ScheduleClearCacheCommand::class,
        'ScheduleTest'       => ScheduleTestCommand::class,
        'ScheduleWork'       => ScheduleWorkCommand::class,
    ];

    public function configure(): void
    {
        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        foreach (array_keys($this->commands) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values($this->commands));
    }

    protected function commands(array $commands): void
    {
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    protected function registerScheduleClearCacheCommand(): void
    {
        $this->app->singleton(ScheduleClearCacheCommand::class);
    }

    protected function registerScheduleFinishCommand(): void
    {
        $this->app->singleton(ScheduleFinishCommand::class);
    }

    protected function registerScheduleListCommand(): void
    {
        $this->app->singleton(ScheduleListCommand::class);
    }

    protected function registerScheduleRunCommand(): void
    {
        $this->app->singleton(ScheduleRunCommand::class);
    }

    protected function registerScheduleTestCommand(): void
    {
        $this->app->singleton(ScheduleTestCommand::class);
    }

    protected function registerScheduleWorkCommand(): void
    {
        $this->app->singleton(ScheduleWorkCommand::class);
    }
}
