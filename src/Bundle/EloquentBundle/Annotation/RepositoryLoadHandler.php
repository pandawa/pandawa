<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Annotation;

use Pandawa\Annotations\Eloquent\AsRepository;
use Pandawa\Bundle\AnnotationBundle\Handler\ServiceLoadHandler;
use Pandawa\Bundle\EloquentBundle\EloquentBundle;
use Pandawa\Contracts\Eloquent\Factory\RepositoryFactoryInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RepositoryLoadHandler extends ServiceLoadHandler
{
    /**
     * @param  AsRepository  $annotation
     * @param  ReflectionClass  $class
     *
     * @return array
     */
    protected function makeServiceConfig(mixed $annotation, ReflectionClass $class): array
    {
        return [
            'alias'     => [
                ...((array) $annotation->alias),
                sprintf('Eloquent.%s', $annotation->model)
            ],
            'factory'   => [
                '@' . RepositoryFactoryInterface::class,
                'create',
            ],
            'arguments' => [
                $annotation->model,
                $class->getName(),
            ],
        ];
    }

    protected function getConfigCacheKey(): string
    {
        return EloquentBundle::REPOSITORY_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
