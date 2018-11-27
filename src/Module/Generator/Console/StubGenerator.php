<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console;

use Illuminate\Console\Command;

/**
 * @author Valentino Ekaputra <valentino.ekaputra@live.com>
 */
class StubGenerator extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $stub = '';

    /**
     * @var string
     */
    protected $suffix = '';

    /**
     * @var string
     */
    protected $extension = 'php';

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var int
     */
    protected $argc = 0;

    /**
     * @var string
     */
    protected $app = '';

    /**
     * @var string
     */
    protected $module = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $type = null;

    /**
     * StubGenerator constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        $this->args = explode(':', $this->argument('args'));
        $this->argc = count($this->args);

        $this->app = $this->option('app') ?? config('modules.generator.app');

        $this->module = $this->args[0];
        $this->name = $this->args[1];
    }

    /**
     * @param $data
     * @return bool
     */
    protected function putFile($data): bool
    {
        if (!is_dir($this->getDirectory())) {
            mkdir($this->getDirectory(), 0777, true);
        }

        if (file_exists($this->getDirectory().'/'.$this->getFileName())) {
            $this->error($this->getFileName().' already exists!');
            return false;
        }

        file_put_contents(
            $this->getDirectory().'/'.$this->getFileName(),
            $data
        );

        return true;
    }

    /**
     * @param array $search
     * @param array $replace
     * @return string
     */
    protected function getData(array $search = [], array $replace = []): string
    {
        return str_replace($search, $replace, $this->getStub());
    }

    /**
     * @return string
     */
    protected function getDirectory(): string
    {
        return base_path().'/src/'.str_replace('\\', '/', $this->getNamespace());
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return $this->getClassName().'.'.$this->extension;
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getRawClassName().$this->suffix;
    }

    /**
     * @return string
     */
    protected function getRawClassName(): string
    {
        return array_last(explode('\\', $this->name));
    }

    /**
     * @param null $type
     * @return string
     */
    protected function getNamespace($type = null): string
    {
        return $this->app.'\\'.$this->module.$this->getType($type).$this->getGroup();
    }

    /**
     * @param null $type
     * @return string
     */
    protected function getType($type = null): string
    {
        $result = $type ?? $this->type ?? null;
        return null != $result ? '\\'.$result : '';
    }

    /**
     * @return string
     */
    protected function getGroup(): string
    {
        $names = explode('\\', $this->name);
        if (count($names) == 1) {
            return '';
        }

        return '\\'.implode('\\', array_slice($names, 0, count($names) - 1));
    }

    /**
     * @param null $type
     * @param string $className
     * @return string
     */
    protected function getInclude($type = null, $className = ''): string
    {
        return $this->getNamespace($type).'\\'.$className;
    }

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . "/Stubs/Pandawa" . $this->stub . ".stub");
    }
}

