<?php

declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Illuminate\Database\Migrations\Migrator;
use Pandawa\Component\Module\AbstractModule;

/**
 * @mixin AbstractModule
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait MigrationProviderTrait
{
    protected $migrationPath = 'Resources/database/migrations';

    public function bootMigrationProvider(): void
    {
        if ($this->app->runningInConsole()) {
            $this->migrator()->path($this->getCurrentPath() . '/' . $this->migrationPath);
        }
    }

    protected function migrator(): Migrator
    {
        return $this->app->get('migrator');
    }
}
