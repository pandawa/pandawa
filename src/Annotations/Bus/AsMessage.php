<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Bus;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class AsMessage
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $normalizer = null,
        public readonly ?string $denormalizer = null,
        public readonly ?array $stamps = [],
    ) {
    }
}
