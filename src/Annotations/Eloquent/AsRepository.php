<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Eloquent;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class AsRepository
{
    public function __construct(
        public readonly string $model,
        public readonly array|string $alias = []
    ) {
    }
}
