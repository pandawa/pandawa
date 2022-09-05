<?php

declare(strict_types=1);

namespace Pandawa\Component\Annotation;

use Pandawa\Component\Annotation\Exception\AnnotationException;
use Pandawa\Contracts\Annotation\AnnotationLoaderInterface;
use ReflectionClass;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

/**
 * @template TAnnotation
 * @implements AnnotationLoaderInterface<TAnnotation>
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AnnotationLoader implements AnnotationLoaderInterface
{
    public function __construct(
        protected readonly ClassesInterface $classLocator,
        protected readonly ReaderInterface $reader,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $annotationClass, ?string $targetClass = null): array
    {
        return array_filter(
            array_map(
                function (ReflectionClass $class) use ($annotationClass) {
                    if (null !== $annotation = $this->makeAnnotation($class, $annotationClass)) {
                        return [
                            'class'      => $class,
                            'annotation' => $annotation,
                        ];
                    }

                    return null;
                },
                $this->classLocator->getClasses($targetClass),
            )
        );
    }

    /**
     * @param  ReflectionClass  $class
     * @param  class-string<TAnnotation>  $annotationClass
     *
     * @return TAnnotation|null
     */
    protected function makeAnnotation(ReflectionClass $class, string $annotationClass): ?object
    {
        try {
            return $this->reader->firstClassMetadata($class, $annotationClass);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
