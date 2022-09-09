<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Auth;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class AsPolicy
{
    public function __construct(public readonly string $model)
    {
    }
}
