<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

use Pandawa\Module\Generator\Console\Arguments\RepositoryArgumentTrait;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeQueryHandlerConsole extends StubGenerator
{
    use RepositoryArgumentTrait;

    /**
     * @var string
     */
    protected $signature = 'pandawa:make:query-handler {args} {repo?} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa query handler files';

    /**
     * @var string
     */
    protected $stub = 'QueryHandler';

    /**
     * @var string
     */
    protected $suffix = 'Handler';

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
            '{{className}}',
            '{{queryClassName}}',
        ];

        $replace = [
            config('modules.generator.author'),
            $this->getNamespace(),
            $this->getClassName(),
            $this->getRawClassName(),
        ];

        if (null != $this->argument('repo')) {
            $this->stub = 'QueryHandlerWithRepository';

            $search = array_merge(
                $search,
                [
                    '{{repositoryName}}',
                    '{{repositoryNamespace}}',
                ]
            );

            $replace = array_merge(
                $replace,
                [
                    $this->getRepositoryName(),
                    $this->getRepositoryNamespace(),
                ]
            );
        }

        if ($this->putFile($this->getData($search, $replace))) {
            $this->info($this->getClassName().' created!');
        }
    }
}

