<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Annotation;

use ReflectionClass;

/**
 * @template TAnnotation
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AnnotationLoaderInterface
{
    /**
     * @param  class-string<TAnnotation>  $annotationClass
     * @param  string|null  $targetClass
     *
     * @return array{class: ReflectionClass, annotation: TAnnotation}
     */
    public function load(string $annotationClass, ?string $targetClass = null): array;
}
