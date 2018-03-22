<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api\Routing\Loader;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Pandawa\Component\Resource\ResourceRegistryInterface;
use Pandawa\Module\Api\Http\Controller\ResourceControllerInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ResourceLoader extends AbstractLoader
{
    const AVAILABLE_RESOURCES = ['index', 'show', 'store', 'update', 'destroy'];

    const RESOURCE_METHODS = [
        'index'   => ['get'],
        'show'    => ['get'],
        'store'   => ['post'],
        'update'  => ['patch', 'put'],
        'destroy' => ['delete'],
    ];

    const NEED_ID = ['show', 'update', 'destroy'];

    /**
     * @var string
     */
    private $resourceController;

    /**
     * @var ResourceRegistryInterface
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param string                    $resourceController
     * @param ResourceRegistryInterface $registry
     */
    public function __construct(string $resourceController, ResourceRegistryInterface $registry = null)
    {
        if (!in_array(ResourceControllerInterface::class, class_implements($resourceController))) {
            throw new RuntimeException(
                sprintf(
                    'Controller "%s" should implement "%s"',
                    $resourceController,
                    ResourceControllerInterface::class
                )
            );
        }

        $this->resourceController = $resourceController;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoutes(string $type, string $path, string $controller, array $route): array
    {
        if (null === $this->registry) {
            throw new RuntimeException('There are not registry class registered. Please enable PandawaResourceModule');
        }

        if (null === $resource = array_get($route, 'resource')) {
            $resource = $this->getResourceNameFromPath($path);
        }

        if (!$this->registry->has($resource)) {
            throw new RuntimeException(sprintf('Resource "%s" is not registered.', $resource));
        }

        $routes = [];

        foreach ($this->getAllowedResources($route) as $type) {
            $resourceController = sprintf('%s@%s', $controller, $type);

            $methods = self::RESOURCE_METHODS[$type];
            $targetPath = $path;

            if (in_array($type, self::NEED_ID)) {
                $targetPath = sprintf('%s/{%s}', $path, $resource);
            }

            /** @var Route $route */
            $route = Route::match($methods, $targetPath, $resourceController);
            $route->name($this->fixRouteName($path, $type));
            $route->defaults = array_merge($route->defaults, ['type' => $type]);

            $routes[] = $route;
        }

        if (empty($routes)) {
            throw new RuntimeException('There are not routes detected.');
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function support(string $type): bool
    {
        return 'resource' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteDefaultParameters(array $route): array
    {
        $path = array_get($route, 'path');
        $resource = array_get($route, 'resource', $this->getResourceNameFromPath($path));

        return ['resource' => $resource];
    }

    protected function applyOption(string $option, $routeObject, array $route, array $values): void
    {
        if ('middleware' === $option) {
            if (!empty($values)) {
                if (!empty($middlewareOptions = array_get($route, 'options.middleware'))) {
                    $middlewareValues = $values;
                    $values = [];

                    foreach ($middlewareValues as $index => $middleware) {
                        if (!array_key_exists($middleware, $middlewareOptions)) {
                            $values[] = $middleware;

                            continue;
                        }

                        $currentMiddlewareOptions = $middlewareOptions[$middleware];
                        $routeName = $routeObject->getName();
                        $routeAction = substr($routeName, strpos($routeName, '.') + 1);

                        if (!empty($only = (array) array_get($currentMiddlewareOptions, 'only'))) {
                            if (in_array($routeAction, $only)) {
                                $values[] = $middleware;
                            }
                        } else if (!empty($except = (array) array_get($currentMiddlewareOptions, 'except'))) {
                            if (!in_array($routeAction, $except)) {
                                $values[] = $middleware;
                            }
                        }
                    }
                }
            }
        }

        parent::applyOption($option, $routeObject, $route, $values);
    }

    /**
     * {@inheritdoc}
     */
    protected function getController(array $route): string
    {
        return $this->resourceController;
    }

    private function fixRouteName(string $path, string $action): string
    {
        $path = str_replace('/', '-', $path);

        return sprintf('%s.%s', $path, $action);
    }

    /**
     * Get allowed resources.
     *
     * @param array $route
     *
     * @return array
     */
    private function getAllowedResources(array $route): array
    {
        $resources = self::AVAILABLE_RESOURCES;

        if (!empty($only = (array) array_get($route, 'only'))) {
            return array_filter(
                $resources,
                function (string $resource) use ($only) {
                    return in_array($resource, $only, true);
                }
            );
        }

        if (!empty($except = (array) array_get($route, 'except'))) {
            return array_filter(
                $resources,
                function (string $resource) use ($except) {
                    return !in_array($resource, $except, true);
                }
            );
        }

        return $resources;
    }

    /**
     * Get resource name.
     *
     * @param string $path
     *
     * @return string
     */
    private function getResourceNameFromPath(string $path): string
    {
        $resource = substr($path, (int) strrpos($path, '/'));
        $resource = str_replace('-', '_', $resource);

        return trim(Str::singular($resource), '/');
    }
}
