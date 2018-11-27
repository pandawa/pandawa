<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeRelationshipConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:relation {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa relationship';

    /**
     * @var string
     */
    protected $stub = 'Relationship';

    /**
     * @var string
     */
    protected $suffix = 'Trait';

    /**
     * @var string
     */
    protected $type = 'Relation';

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

