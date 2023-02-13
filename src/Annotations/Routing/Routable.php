<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Routing;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Routable
{
    public function __construct(
        public readonly string $prefix = '',
        public readonly ?string $routeGroup = null,
        public readonly array|string|null $middleware = null,
    ) {
    }
}
