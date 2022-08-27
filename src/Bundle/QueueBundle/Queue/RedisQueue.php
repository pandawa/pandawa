<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Queue;

use Illuminate\Queue\RedisQueue as LaravelRedisQueue;
use Pandawa\Component\Queue\Queue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisQueue extends LaravelRedisQueue
{
    use Queue;
}
