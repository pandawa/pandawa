<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DatabaseBundle;

use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Console\DumpCommand;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Database\MigrationServiceProvider;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabaseBundle extends Bundle
{
    protected array $commands = [
        DbCommand::class,
        PruneCommand::class,
        WipeCommand::class,
        DumpCommand::class,
        SeedCommand::class,
    ];

    protected array $devCommands = [
        SeederMakeCommand::class,
    ];

    protected array $providers = [
        MigrationServiceProvider::class,
    ];

    public function configure(): void
    {
        $this->app->loadConsoles([...$this->devCommands, ...$this->commands]);
    }

    protected function plugins(): array
    {
        return [
            new RegisterBundlesPlugin($this->providers),
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }
}
