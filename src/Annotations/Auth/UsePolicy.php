<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Auth;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class UsePolicy
{
    public function __construct(public readonly string $policy)
    {
    }
}
