<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeListenerConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:listener {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa listener files';

    /**
     * @var string
     */
    protected $stub = 'Listener';

    /**
     * @var string
     */
    protected $suffix = 'Notification';

    protected $type = 'Listener';

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
            '{{eventNamespace}}',
            '{{eventClassName}}',
        ];

        $replace = [
            config('modules.generator.author'),
            $this->getNamespace(),
            $this->getClassName(),
            $this->getInclude('Event', $this->getRawClassName()),
            $this->getRawClassName(),
        ];

        if ($this->putFile($this->getData($search, $replace))) {
            $this->info($this->getClassName().' created!');
        }
    }
}

