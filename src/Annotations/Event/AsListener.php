<?php

declare(strict_types=1);

namespace Pandawa\Annotations\Event;

use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class AsListener
{
    public function __construct(public readonly string $event)
    {
    }
}
