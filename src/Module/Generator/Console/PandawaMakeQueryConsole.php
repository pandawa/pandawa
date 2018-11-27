<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

use Illuminate\Support\Facades\Artisan;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeQueryConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:query {args} {repo?} {--app=} {--handler}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa query files';

    /**
     * @var string
     */
    protected $stub = 'Query';

    /**
     * @var string
     */
    protected $type = 'Query';

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
                'pandawa:make:query-handler',
                [
                    'args' => $this->argument('args'),
                    'repo' => $this->argument('repo'),
                    '--app' => $this->option('app')
                ],
                $this->getOutput()
            );
        }
    }
}

