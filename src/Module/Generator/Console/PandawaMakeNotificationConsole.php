<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakeNotificationConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:notification {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa notification files';

    /**
     * @var string
     */
    protected $stub = 'Notification';

    /**
     * @var string
     */
    protected $suffix = 'Notification';

    /**
     * @var string
     */
    protected $type = 'Notification';

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

