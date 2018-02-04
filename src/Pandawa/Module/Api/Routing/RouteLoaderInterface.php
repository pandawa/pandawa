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

namespace Pandawa\Module\Api\Routing;

use Pandawa\Module\Api\Routing\Loader\LoaderTypeInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RouteLoaderInterface
{
    public function add(LoaderTypeInterface $loader, int $priority = 0): void;

    public function loadFile(string $file): void;

    public function load(array $routes): void;
}
