<?php

declare(strict_types=1);

namespace Pandawa\Component\Queue\Stamp;

use Pandawa\Contracts\Bus\StampInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TimeoutStamp implements StampInterface
{
    public function __construct(public readonly int $timeout)
    {
    }
}
