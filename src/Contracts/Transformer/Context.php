<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Transformer;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Context
{
    public function __construct(
        public readonly array $includes = [],
        public readonly array $selects = [],
        public readonly string|int|null $version = null,
        public readonly ?Request $request = null,
    ) {
    }
}
