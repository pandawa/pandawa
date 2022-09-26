<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus\Message;

use Pandawa\Contracts\Bus\StampInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Metadata
{
    /**
     * @param array<int, list<StampInterface>> $stamps
     */
    public function __construct(
        public readonly string $class,
        public readonly ?string $name = null,
        public readonly ?string $serializer = null,
        public readonly array $stamps = [],
    ) {
    }
}
