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
        unset($route['path']);
        unset($route['controller']);
        unset($route['model']);
        unset($route['rules']);
        unset($route['method']);

        foreach (['only', 'except', 'names'] as $index) {
            if (isset($route[$index])) {
                $options[$index] = $route[$index];
                unset($route[$index]);
            }
        }

        $routeObject = $this->createRoute($type, $path, $controller, $options, $route);

        foreach (['middleware', 'name'] as $index) {
            if (isset($route[$index]) && $route[$index]) {
                call_user_func_array([$routeObject, $index], (array) $route[$index]);
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
     * @param string $type
     * @param string $path
     * @param string $controller
     * @param array  $options
     * @param array  $route
     *
     * @return Route|mixed
     */
    abstract protected function createRoute(string $type, string $path, string $controller, array $options, array $route);
}
