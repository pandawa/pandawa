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

use Pandawa\Module\Api\Routing\RouteLoader;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface LoaderAwareInterface
{
    /**
     * Set route loader.
     *
     * @param RouteLoader $loader
     */
    public function setLoader(RouteLoader $loader): void;
}
