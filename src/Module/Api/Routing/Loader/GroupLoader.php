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

use Closure;
use Route;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class GroupLoader implements LoaderTypeInterface, LoaderAwareInterface
{
    use LoaderAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function support(string $type): bool
    {
        return 'group' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $route): void
    {
        if (!isset($route['children']) || empty($route['children'])) {
            throw new RuntimeException('Index "children" should be defined and cannot be empty.');
        }

        $config = [];
        foreach (['middleware', 'namespace', 'prefix', 'where'] as $index) {
            if (isset($route[$index]) && $route[$index]) {
                $config[$index] = $route[$index];
            }
        }

        Route::group($config, $this->loadChildren($route));
    }

    /**
     * Load route children handler.
     *
     * @param array $route
     *
     * @return Closure
     */
    private function loadChildren(array $route): Closure
    {
        return function () use ($route) {
            $children = array_get($route, 'children');

            if (is_string($children)) {
                $this->loader->loadFile($children);

                return;
            }

            if (is_array($children)) {
                $this->loader->load($children);

                return;
            }

            throw new RuntimeException(
                sprintf(
                    'Unsupported type "%s" for index "children"',
                    gettype($route['children'])
                )
            );
        };
    }
}
