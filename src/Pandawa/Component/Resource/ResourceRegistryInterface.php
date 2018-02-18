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

namespace Pandawa\Component\Resource;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ResourceRegistryInterface
{
    public function add(string $resource, Metadata $metadata): void;

    public function has(string $resource): bool;

    public function remove(string $resource): void;

    public function get(string $resource): Metadata;

}
