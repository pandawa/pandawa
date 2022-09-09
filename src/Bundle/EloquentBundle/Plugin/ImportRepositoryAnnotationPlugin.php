<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Plugin;

use Pandawa\Annotations\Eloquent\AsRepository;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationServicePlugin;
use Pandawa\Bundle\EloquentBundle\Annotation\RepositoryLoadHandler;
use Pandawa\Bundle\EloquentBundle\EloquentBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportRepositoryAnnotationPlugin extends AnnotationServicePlugin
{
    protected ?string $defaultPath = 'Repository';

    protected function getAnnotationClasses(): array
    {
        return [AsRepository::class];
    }

    protected function getHandler(): string
    {
        return RepositoryLoadHandler::class;
    }

    protected function getConfigCacheKey(): string
    {
        return EloquentBundle::REPOSITORY_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
