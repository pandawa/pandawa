<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeRepositoryConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:repo {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa repository';

    /**
     * @var string
     */
    protected $stub = 'Repository';

    /**
     * @var string
     */
    protected $suffix = 'Repository';

    /**
     * @var string
     */
    protected $type = 'Repository';

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

