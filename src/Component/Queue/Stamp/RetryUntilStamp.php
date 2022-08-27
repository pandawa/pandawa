<?php

declare(strict_types=1);

namespace Pandawa\Component\Queue\Stamp;

use Pandawa\Contracts\Bus\StampInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RetryUntilStamp implements StampInterface
{
    public readonly int $expiration;

    public function __construct(string $interval)
    {
        $this->expiration = now()->modify($interval)->getTimestamp();
    }
}
