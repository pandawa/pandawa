<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeCommandHandlerConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:command-handler {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa command handler files';

    /**
     * @var string
     */
    protected $stub = 'CommandHandler';

    /**
     * @var string
     */
    protected $suffix = 'Handler';

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
            '{{className}}',
            '{{commandClassName}}'
        ];

        $replace = [
            config('modules.generator.author'),
            $this->getNamespace(),
            $this->getClassName(),
            $this->getRawClassName()
        ];

        if ($this->putFile($this->getData($search, $replace))) {
            $this->info($this->getClassName().' created!');
        }
    }
}

