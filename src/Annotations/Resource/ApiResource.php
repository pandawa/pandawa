<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Resource;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class ApiResource
{
    /**
     * @param  array{
     *     resource_key: string,
     *     default_content_type: string,
     *     index: array{
     *          http_code: integer,
     *          paginate: integer,
     *          rules: string[],
     *          filters: array<string, string|array>,
     *          repository: array{call: string, arguments: array<string, scalar>},
     *          criteria: list<array{class: string, arguments: array<string, scalar>}>,
     *          transformer: array{
     *              class: string,
     *              context: array{
     *                  available_includes: scalar[],
     *                  default_includes: scalar[],
     *                  available_selects: scalar[],
     *                  default_selects: scalar[],
     *              }
     *          },
     *          serialize: array{context: array}
     *     },
     *     show: array{
     *          http_code: integer,
     *          rules: string[],
     *          filters: array<string, string|array>,
     *          repository: array{call: string, arguments: array<string, scalar>},
     *          criteria: list<array{class: string, arguments: array<string, scalar>}>,
     *          transformer: array{
     *              class: string,
     *              context: array{
     *                  available_includes: scalar[],
     *                  default_includes: scalar[],
     *                  available_selects: scalar[],
     *                  default_selects: scalar[],
     *              }
     *          },
     *          serialize: array{context: array}
     *     },
     *     store: array{
     *          http_code: integer,
     *          rules: string[],
     *          transformer: array{
     *              class: string,
     *              context: array{
     *                  available_includes: scalar[],
     *                  default_includes: scalar[],
     *                  available_selects: scalar[],
     *                  default_selects: scalar[],
     *              }
     *          },
     *          serialize: array{context: array}
     *     },
     *     update: array{
     *          http_code: integer,
     *          rules: string[],
     *          transformer: array{
     *              class: string,
     *              context: array{
     *                  available_includes: scalar[],
     *                  default_includes: scalar[],
     *                  available_selects: scalar[],
     *                  default_selects: scalar[],
     *              }
     *          },
     *          serialize: array{context: array}
     *     },
     *     delete: array{
     *          http_code: integer,
     *          rules: string[],
     *          transformer: array{
     *              class: string,
     *              context: array{
     *                  available_includes: scalar[],
     *                  default_includes: scalar[],
     *                  available_selects: scalar[],
     *                  default_selects: scalar[],
     *              }
     *          },
     *          serialize: array{context: array}
     *     }
     * } $options
     */
    public function __construct(
        public readonly string $uri,
        public readonly ?string $routeName = null,
        public readonly array $only = [],
        public readonly array $except = [],
        public readonly array $options = [],
    ) {
    }
}
