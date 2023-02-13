<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Annotation;

use Pandawa\Annotations\Routing\Routable;
use Pandawa\Annotations\Routing\Route;
use Pandawa\Bundle\AnnotationBundle\Handler\RouteLoadHandler as BaseRouteLoadHandler;
use Pandawa\Contracts\Annotation\Factory\ReaderFactoryInterface;
use Pandawa\Contracts\Routing\LoaderResolverInterface;
use ReflectionClass;
use ReflectionFunctionAbstract;
use Spiral\Attributes\ReaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteLoadHandler extends BaseRouteLoadHandler
{
    private ReaderInterface $reader;

    public function __construct(
        LoaderResolverInterface $routeLoaderResolver,
        ReaderFactoryInterface $readerFactory,
    ) {
        parent::__construct($routeLoaderResolver);

        $this->reader = $readerFactory->create();
    }

    /**
     * @param  Routable  $annotation
     * @param  ReflectionClass  $class
     *
     * @return array
     */
    protected function makeRouteConfig(mixed $annotation, ReflectionClass $class): array
    {
        $routes = [];

        foreach (($class->getMethods() ?? []) as $method) {
            if (null !== $routeAnnotation = $this->getRouteAnnotation($method)) {
                $routes[] = array_filter([
                    'uri'        => rtrim($annotation->prefix, '/').'/'.ltrim($routeAnnotation->uri, '/'),
                    'name'       => $routeAnnotation->routeName,
                    'group'      => $routeAnnotation->routeGroup ?? $annotation->routeGroup,
                    'methods'    => $routeAnnotation->methods,
                    'controller' => $class->getName().'@'.$method->getName(),
                    'middleware' => array_filter(
                        [
                            ...(array) $annotation->middleware,
                            ...(array) $routeAnnotation->middleware,
                        ]
                    ),
                    'options'    => $routeAnnotation->options,
                ]);
            }
        }

        return $routes;
    }

    private function getRouteAnnotation(ReflectionFunctionAbstract $method): ?Route
    {
        return $this->reader->firstFunctionMetadata($method, Route::class);
    }
}
