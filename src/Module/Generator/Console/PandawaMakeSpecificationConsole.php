<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeSpecificationConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:spec {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa specification files';

    /**
     * @var string
     */
    protected $stub = 'Specification';

    /**
     * @var string
     */
    protected $suffix = 'Specification';

    /**
     * @var string
     */
    protected $type = 'Specification';

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

