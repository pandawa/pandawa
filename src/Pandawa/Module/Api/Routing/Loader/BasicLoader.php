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

use Route;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BasicLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    protected function createRoutes(string $type, string $path, string $controller, array $route): array
    {
        return [Route::{$type}($path, $controller)];
    }

    /**
     * {@inheritdoc}
     */
    public function support(string $type): bool
    {
        return in_array($type, ['get', 'post', 'put', 'patch', 'delete', 'option', 'head']);
    }
}
