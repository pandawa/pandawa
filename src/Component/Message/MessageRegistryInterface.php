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

namespace Pandawa\Component\Message;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface MessageRegistryInterface
{
    public function add(string $message, Metadata $metadata): void;

    public function has(string $message): bool;

    public function remove(string $message): void;

    public function get(string $message): Metadata;
}
