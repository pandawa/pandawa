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

use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ResourceRegistry implements ResourceRegistryInterface
{
    /**
     * @var Metadata[]
     */
    private $resources = [];

    public function add(string $resource, Metadata $metadata): void
    {
        if ($this->has($resource)) {
            throw new RuntimeException(sprintf('Resource "%s" already registered.', $resource));
        }

        $this->resources[$resource] = $metadata;
    }

    public function has(string $resource): bool
    {
        return array_key_exists($resource, $this->resources);
    }

    public function remove(string $resource): void
    {
        $this->assertExists($resource);

        unset($this->resources[$resource]);
    }

    public function get(string $resource): Metadata
    {
        $this->assertExists($resource);

        return $this->resources[$resource];
    }

    private function assertExists(string $resource): void
    {
        if (!$this->has($resource)) {
            throw new RuntimeException(sprintf('Resource "%s" is not registered.', $resource));
        }
    }
}
