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
        'destroy' => ['get'],
    ];

    /**
     * @var string
     */
    private $resourceController;

    /**
     * Constructor.
     *
     * @param string $resourceController
     */
    public function __construct(string $resourceController)
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
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoutes(string $type, string $path, string $controller, array $route): array
    {
        if (null === $model = array_get($route, 'model')) {
            throw new RuntimeException(sprintf('Parameter "model" required for route resource on path "%s"', $path));
        }

        $routes = [];

        foreach ($this->getAllowedResources($route) as $type) {
            $resourceController = sprintf('%s@%s', $controller, $type);

            foreach (self::RESOURCE_METHODS[$type] as $methods) {
                /** @var Route $route */
                $route = Route::match($methods, $path, $resourceController);
                $route->name($this->fixRouteName($path, $type));
                $route->defaults = array_merge($route->defaults, ['type' => $type]);

                $routes[] = $route;
            }
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
        return ['model' => array_get($route, 'model')];
    }

    protected function applyOption(string $option, $routeObject, array $route, array $values): void
    {
        if ('middleware' === $option) {
            if (!empty($values)) {
                if (!empty($middlewareOptions = array_get($route, 'options.middleware'))) {
                    if (!empty($only = (array) array_get($middlewareOptions, 'only'))) {
                        $values = array_filter(
                            $values,
                            function (string $middleware) use ($only) {
                                return in_array($middleware, $only, true);
                            }
                        );
                    } else if (!empty($except = (array) array_get($route, 'except'))) {
                        $values = array_filter(
                            $values,
                            function (string $middleware) use ($only) {
                                return !in_array($middleware, $only, true);
                            }
                        );
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
}
