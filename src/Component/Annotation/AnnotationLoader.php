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
        $annotations = [];

        foreach ($this->classLocator->getClasses($targetClass) as $class) {
            foreach ($this->loadAnnotations($class, $annotationClass) as $annotation) {
                $annotations[] = [
                    'class'      => $class,
                    'annotation' => $annotation,
                ];
            }
        }

        return $annotations;
    }

    /**
     * @param  ReflectionClass  $class
     * @param  class-string<TAnnotation>  $annotationClass
     *
     * @return array<TAnnotation>
     */
    protected function loadAnnotations(ReflectionClass $class, string $annotationClass): iterable
    {
        try {
            return $this->reader->getClassMetadata($class, $annotationClass);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
