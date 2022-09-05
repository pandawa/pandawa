<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AnnotationBundle\Handler;

use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use Pandawa\Contracts\Routing\LoaderResolverInterface;
use ReflectionClass;

/**
 * @template TAnnotation
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class RouteLoadHandler implements AnnotationLoadHandlerInterface
{
    protected BundleInterface $bundle;

    public function __construct(protected readonly LoaderResolverInterface $routeLoaderResolver)
    {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: TAnnotation}  $options
     */
    public function handle(array $options): void
    {
        tap($this->makeRouteConfig($options['annotation'], $options['class']), function (array $routeConfig) {
            $this->routeLoaderResolver->resolve($routeConfig)->load($routeConfig);
        });
    }

    abstract protected function makeRouteConfig(mixed $annotation, ReflectionClass $class): array;

}
