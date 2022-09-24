<?php

declare(strict_types=1);

namespace Pandawa\Component\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class NoneObjectEvent
{
    public function __construct(public readonly mixed $event)
    {
    }
}
