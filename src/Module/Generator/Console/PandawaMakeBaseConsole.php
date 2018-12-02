<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

use Illuminate\Support\Facades\Artisan;

class PandawaMakeBaseConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:base {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa basic module files';

    /**
     * @return mixed
     */
    public function handle()
    {
        parent::handle();

        $this->info('Creating basic files for pandawa module');

        Artisan::call(
            'pandawa:make:module',
            [
                'args' => $this->argument('args'),
                '--app' => $this->option('app')
            ],
            $this->getOutput()
        );

        Artisan::call(
            'pandawa:make:model',
            [
                'args' => $this->argument('args'),
                '--app' => $this->option('app')
            ],
            $this->getOutput()
        );

        Artisan::call(
            'pandawa:make:repo',
            [
                'args' => $this->argument('args'),
                '--app' => $this->option('app')
            ],
            $this->getOutput()
        );
    }
}

