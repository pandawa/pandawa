<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Routing;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD), NamedArgumentConstructor]
final class Route
{
    public function __construct(
        public readonly string $uri,
        public readonly array|string $methods,
        public readonly ?string $routeName = null,
        public readonly ?string $routeGroup = null,
        public readonly array|string|null $middleware = null,
        public readonly ?array $options = null,
    ) {
    }
}
