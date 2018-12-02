<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

use Illuminate\Support\Facades\Artisan;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeEventConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:event {args} {--app=} {--listener}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa event files';

    /**
     * @var string
     */
    protected $stub = 'Event';

    /**
     * @var string
     */
    protected $type = 'Event';

    /**
     * @return mixed
     */
    public function handle()
    {
        parent::handle();

        $search = [
            '{{author}}',
            '{{namespace}}',
            '{{className}}'
        ];

        $replace = [
            config('modules.generator.author'),
            $this->getNamespace(),
            $this->getClassName()
        ];

        if ($this->putFile($this->getData($search, $replace))) {
            $this->info($this->getClassName().' created!');
        }

        if ($this->option('listener')) {
            Artisan::call(
                'pandawa:make:listener',
                [
                    'args' => $this->argument('args'),
                    '--app' => $this->option('app')
                ],
                $this->getOutput()
            );
        }
    }
}

