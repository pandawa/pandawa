<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeModelConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:model {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa model';

    /**
     * @var string
     */
    protected $stub = 'Model';

    /**
     * @var string
     */
    protected $type = 'Model';

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

