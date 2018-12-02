<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeMailConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:mail {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa mail files';

    /**
     * @var string
     */
    protected $stub = 'Mail';

    /**
     * @var string
     */
    protected $suffix = 'Mail';

    /**
     * @var string
     */
    protected $type = 'Mail';

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

