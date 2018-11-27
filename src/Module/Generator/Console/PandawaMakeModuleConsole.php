<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeModuleConsole extends StubGenerator
{

    /**
     * @var string
     */
    protected $signature = 'pandawa:make:module {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa module files';

    /**
     * @var string
     */
    protected $stub = 'Module';

    /**
     * @var string
     */
    protected $suffix = 'Module';

    /**
     * @var string
     */
    protected $extension = 'php';

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
    }
}

