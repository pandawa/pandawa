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

namespace Pandawa\Component\Identifier;

use Pandawa\Component\Serializer\DeserializableInterface;
use Pandawa\Component\Serializer\SerializableInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface IdentifierInterface extends SerializableInterface, DeserializableInterface
{
    /**
     * Generate new identifier.
     *
     * @return IdentifierInterface
     */
    public static function generate(): IdentifierInterface;

    /**
     * Check that given id is equal with current.
     *
     * @param IdentifierInterface $id
     *
     * @return bool
     */
    public function equals(IdentifierInterface $id): bool;
}
