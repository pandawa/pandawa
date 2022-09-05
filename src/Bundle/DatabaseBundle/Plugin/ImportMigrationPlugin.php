<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DatabaseBundle\Plugin;

use Illuminate\Database\Migrations\Migrator;
use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportMigrationPlugin extends Plugin
{
    public function __construct(protected readonly string $migrationPath = 'Resources/database/migrations')
    {
    }

    public function boot(): void
    {
        if ($this->bundle->getApp()->runningInConsole()) {
            $this->migrator()->path($this->bundle->getPath($this->migrationPath));
        }
    }

    protected function migrator(): Migrator
    {
        return $this->bundle->getService('migrator');
    }
}
