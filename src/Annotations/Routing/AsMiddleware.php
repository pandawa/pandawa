<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Routing;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class AsMiddleware
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $group = null,
    ) {
    }
}
