<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Annotation;

use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @template TAnnotation
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AnnotationLoadHandlerInterface
{
    /**
     * Set active bundle.
     *
     * @param  BundleInterface  $bundle
     *
     * @return void
     */
    public function setBundle(BundleInterface $bundle): void;

    /**
     * Handle annotation.
     *
     * @param  array{class: ReflectionClass, annotation: TAnnotation}  $options
     *
     * @return void
     */
    public function handle(array $options): void;
}
