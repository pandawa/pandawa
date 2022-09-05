<?php

declare(strict_types=1);

namespace Pandawa\Annotations\DependencyInjection;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Injectable
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly array|string $alias = [],
        public readonly ?string $tag = null,
        public readonly bool $deffer = false,
    ) {
    }
}
