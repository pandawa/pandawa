<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Resource;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class ApiMessage
{
    /**
     * @param array{
     *     default_content_type: string,
     *     http_code: integer,
     *     rules: string[],
     *     paginate: integer,
     *     criteria: list<array{class: string, arguments: array<string, scalar>}>,
     *     transformer: array{
     *          class: string,
     *          context: array{
     *              available_includes: scalar[],
     *              default_includes: scalar[],
     *              available_selects: scalar[],
     *              default_selects: scalar[],
     *          }
     *     },
     *     serialize: array{context: array}
     * } $options
     */
    public function __construct(
        public readonly string $uri,
        public readonly array|string $methods,
        public readonly ?string $routeName = null,
        public readonly array $options = [],
    ) {
    }
}
