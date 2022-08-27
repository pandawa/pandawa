<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Queue;

use Illuminate\Queue\SyncQueue as LaravelSyncQueue;
use Pandawa\Component\Queue\Queue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SyncQueue extends LaravelSyncQueue
{
    use Queue;
}
