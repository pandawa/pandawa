<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Stamp;

use DateInterval;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Pandawa\Contracts\Bus\StampInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class QueuedStamp implements StampInterface
{
    use Queueable;

    public function __construct(
        ?string $connection = null,
        ?string $queue = null,
        DateTimeInterface|DateInterval|array|int|null $delay = null,
        ?bool $afterCommit = null,
        ?array $chain = [],
    )
    {
        $this
            ->onConnection($connection)
            ->onQueue($queue)
            ->delay($delay)
            ->chain($chain)
        ;

        if (true === $afterCommit) {
            $this->afterCommit();
        }

        if (false === $afterCommit) {
            $this->beforeCommit();
        }
    }
}
