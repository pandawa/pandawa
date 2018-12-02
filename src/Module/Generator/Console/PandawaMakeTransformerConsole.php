<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeTransformerConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:transformer {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa transformer files';

    /**
     * @var string
     */
    protected $stub = 'Transformer';

    /**
     * @var string
     */
    protected $suffix = 'Transformer';

    /**
     * @var string
     */
    protected $type = 'Transformer';

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

