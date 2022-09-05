<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ResourceBundle\Annotation;

use Illuminate\Support\Str;
use Pandawa\Annotations\Resource\ApiMessage;
use Pandawa\Annotations\Resource\ApiResource;
use Pandawa\Bundle\AnnotationBundle\Handler\RouteLoadHandler;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ApiResourceLoadHandler extends RouteLoadHandler
{
    protected function makeRouteConfig(mixed $annotation, ReflectionClass $class): array
    {
        if ($annotation instanceof ApiResource) {
            return $this->makeRouteResource($annotation, $class);
        }

        return $this->makeRouteMessage($annotation, $class);
    }

    protected function makeRouteMessage(ApiMessage $resource, ReflectionClass $class): array
    {
        return [
            'name'    => $resource->routeName ?? $this->getRouteName($class),
            'type'    => 'message',
            'message' => $class->getName(),
            'uri'     => $resource->uri,
            'methods' => $resource->methods,
            'options' => $resource->options,
        ];
    }

    protected function makeRouteResource(ApiResource $resource, ReflectionClass $class): array
    {
        return [
            'name'     => $resource->routeName ?? $this->getRouteName($class),
            'type'     => 'resource',
            'resource' => $class->getName(),
            'uri'      => $resource->uri,
            'only'     => $resource->only,
            'except'   => $resource->except,
            'options'  => $resource->options,
        ];
    }

    protected function getRouteName(ReflectionClass $class): string
    {
        $className = $class->getName();

        if (method_exists($className, 'resourceName')) {
            return $className::resourceName();
        }

        return Str::snake(preg_replace('/(Model|Entity)$/', '', $class->getShortName()));
    }
}
