<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Queue;

use Illuminate\Queue\DatabaseQueue as LaravelDatabaseQueue;
use Pandawa\Component\Queue\Queue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabaseQueue extends LaravelDatabaseQueue
{
    use Queue;
}
