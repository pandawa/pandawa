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

use Illuminate\Routing\Route;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractLoader implements LoaderTypeInterface
{
    final public function load(array $route): void
    {
        $options = ['defaults' => array_get($route, 'options', [])];
        $path = array_get($route, 'path');
        $type = $this->getType($route);
        $controller = $this->getController($route);

        if (empty($controller)) {
            throw new RuntimeException(sprintf('Undefined index "controller" on route "%s"', json_encode($route)));
        }

        if (null !== $rules = array_get($route, 'rules')) {
            $options['defaults']['rules'] = $rules;
        }

        unset($route['type']);
        unset($route['controller']);
        unset($route['rules']);

        foreach ($this->createRoutes($type, $path, $controller, $route) as $routeObject) {
            $routeObject->defaults = array_merge(
                (array) $routeObject->defaults,
                $options['defaults'],
                $this->getRouteDefaultParameters($route)
            );

            foreach (['middleware', 'name'] as $option) {
                if (isset($route[$option]) && $route[$option]) {
                    $this->applyOption($option, $routeObject, $route, (array) $route[$option]);
                }
            }
        }
    }

    protected function getController(array $route): string
    {
        return array_get($route, 'controller');
    }

    protected function getType(array $route): string
    {
        return strtolower(array_get($route, 'type'));
    }

    /**
     * Apply route option.
     *
     * @param string      $option
     * @param Route|mixed $routeObject
     * @param array       $route
     * @param array       $values
     */
    protected function applyOption(string $option, $routeObject, array $route, array $values): void
    {
        call_user_func_array([$routeObject, $option], $values);
    }

    /**
     * Get custom route parameters.
     *
     * @param array $route
     *
     * @return array
     */
    protected function getRouteDefaultParameters(array $route): array
    {
        return [];
    }

    /**
     * @param string $type
     * @param string $path
     * @param string $controller
     * @param array  $route
     *
     * @return Route[]|mixed
     */
    abstract protected function createRoutes(string $type, string $path, string $controller, array $route): array;
}
