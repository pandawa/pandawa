<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class PandawaMakePresenterConsole extends StubGenerator
{
    /**
     * @var string
     */
    protected $signature = 'pandawa:make:presenter {args} {--app=}';

    /**
     * @var string
     */
    protected $description = 'Generate pandawa presenter files';

    /**
     * @var string
     */
    protected $stub = 'Presenter';

    /**
     * @var string
     */
    protected $suffix = 'Presenter';

    /**
     * @var string
     */
    protected $type = 'Presenter';

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

