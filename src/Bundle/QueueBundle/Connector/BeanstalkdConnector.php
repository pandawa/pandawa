<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Connector;

use Illuminate\Queue\Connectors\BeanstalkdConnector as QueueBeanstalkdConnector;
use Pandawa\Bundle\QueueBundle\Queue\BeanstalkdQueue;
use Pheanstalk\Pheanstalk;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BeanstalkdConnector extends QueueBeanstalkdConnector
{
    public function connect(array $config): BeanstalkdQueue
    {
        return new BeanstalkdQueue(
            $this->pheanstalk($config),
            $config['queue'],
            $config['retry_after'] ?? Pheanstalk::DEFAULT_TTR,
            $config['block_for'] ?? 0,
            $config['after_commit'] ?? null
        );
    }
}
