<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Queue;

use Illuminate\Queue\BeanstalkdQueue as LaravelBeanstalkdQueue;
use Pandawa\Component\Queue\Queue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BeanstalkdQueue extends LaravelBeanstalkdQueue
{
    use Queue;
}
