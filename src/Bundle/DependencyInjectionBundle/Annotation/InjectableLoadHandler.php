<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DependencyInjectionBundle\Annotation;

use Pandawa\Annotations\DependencyInjection\Inject;
use Pandawa\Annotations\DependencyInjection\Injectable;
use Pandawa\Annotations\DependencyInjection\Type;
use Pandawa\Bundle\AnnotationBundle\Handler\ServiceLoadHandler;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use ReflectionClass;
use ReflectionParameter;

/**
 * @extends ServiceLoadHandler<Injectable>
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class InjectableLoadHandler extends ServiceLoadHandler
{
    /**
     * @param  Injectable  $annotation
     */
    protected function makeServiceConfig(mixed $annotation, ReflectionClass $class): array
    {
        $arguments = [];

        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if (null !== $paramAnnotation = $this->getParamAnnotation($parameter)) {
                $arguments[] = $this->parseValue($paramAnnotation);
            }
        }

        return array_filter([
            'service_name' => $annotation->name,
            'class'        => $class->getName(),
            'tag'          => $annotation->tag,
            'alias'        => $annotation->alias,
            'arguments'    => $arguments,
        ]);
    }

    protected function getConfigCacheKey(): string
    {
        return DependencyInjectionBundle::CONFIG_CACHE_KEY.'.injectable.'.$this->bundle->getName();
    }

    protected function getParamAnnotation(ReflectionParameter $parameter): ?Inject
    {
        return $this->reader->firstParameterMetadata($parameter, Inject::class);
    }

    protected function parseValue(Inject $inject): mixed
    {
        return match ($inject->type) {
            Type::CONFIG => '%'.$inject->value.'%',
            Type::SERVICE => '@'.$inject->value,
            Type::VALUE => $inject->value,
        };
    }
}
