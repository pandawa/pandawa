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

namespace Pandawa\Component\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface EventRegistryInterface
{
    public function add(string $eventName, string $eventClass): void;

    public function has(string $eventName): bool;

    public function remove(string $eventName): void;

    public function get(string $eventName): string;
}
