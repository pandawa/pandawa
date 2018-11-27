<?php
declare(strict_types=1);

namespace Pandawa\Module\Generator\Console\Arguments;

trait RepositoryArgumentTrait
{
    public function getRepositoryName(): string
    {
        return array_last(
            explode('\\', $this->getRepositoryNamespace())
        );
    }

    public function getRepositoryNamespace(): string
    {
        return $this->app.'\\'.$this->module.$this->getType('Repository').'\\'.$this->argument('repo').'Repository';
    }
}

