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

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface LoaderTypeInterface
{
    /**
     * Check the current type is supported.
     *
     * @param string $type
     *
     * @return bool
     */
    public function support(string $type): bool;

    /**
     * Load route.
     *
     * @param array $route
     */
    public function load(array $route): void;
}
