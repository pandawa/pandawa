<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Scheduling;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
class AsScheduler
{
}
