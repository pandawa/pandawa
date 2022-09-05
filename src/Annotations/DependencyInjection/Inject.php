<?php

declare(strict_types=1);

namespace Pandawa\Annotations\DependencyInjection;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_PARAMETER|Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class Inject
{
    public function __construct(
        public readonly Type $type,
        public readonly mixed $value,
    ) {
    }
}
