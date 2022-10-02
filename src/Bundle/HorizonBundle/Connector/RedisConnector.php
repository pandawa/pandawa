<?php

declare(strict_types=1);

namespace Pandawa\Bundle\HorizonBundle\Connector;

use Pandawa\Bundle\HorizonBundle\Queue\RedisQueue;
use Pandawa\Bundle\QueueBundle\Connector\RedisConnector as BaseConnector;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisConnector extends BaseConnector
{
    public function connect(array $config): RedisQueue
    {
        return new RedisQueue(
            $this->redis,
            $config['queue'],
            $config['connection'] ?? $this->connection,
            $config['retry_after'] ?? 60,
            $config['block_for'] ?? null,
            $config['after_commit'] ?? null
        );
    }
}
