<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

use Illuminate\Support\Facades\Artisan;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeCommandConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:command {args} {--app=} {--handler}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa command files';

    /**
     * @var string
     */
    protected $stub = 'Command';

    /**
     * @var string
     */
    protected $type = 'Command';

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

        if ($this->option('handler')) {
            Artisan::call(
                'pandawa:make:command-handler',
                [
                    'args' => $this->argument('args'),
                    '--app' => $this->option('app')
                ],
                $this->getOutput()
            );
        }
    }
}

