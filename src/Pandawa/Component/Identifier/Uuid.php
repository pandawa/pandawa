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

use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Uuid implements IdentifierInterface
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * Constructor.
     *
     * @param UuidInterface $uuid
     */
    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * {@inheritdoc}
     */
    public static function generate(): IdentifierInterface
    {
        return new static(RamseyUuid::uuid4());
    }

    /**
     * Create id from string.
     *
     * @param string $id
     *
     * @return Uuid
     */
    public static function fromString(string $id): Uuid
    {
        return new static(RamseyUuid::fromString($id));
    }

    /**
     * {@inheritdoc}
     */
    public function equals(IdentifierInterface $id): bool
    {
        return $id instanceof $this && (string) $id === (string) $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize($id): Uuid
    {
        return static::fromString($id);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return (string) $this->uuid;
    }
}
