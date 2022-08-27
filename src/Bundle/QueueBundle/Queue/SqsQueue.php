<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Queue;

use Illuminate\Queue\SqsQueue as LaravelSqsQueue;
use Pandawa\Component\Queue\Queue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SqsQueue extends LaravelSqsQueue
{
    use Queue;
}
