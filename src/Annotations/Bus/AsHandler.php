<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Bus;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class AsHandler
{
    public function __construct(public readonly string $message)
    {
    }
}
