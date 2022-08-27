<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Connector;

use Illuminate\Queue\Connectors\RedisConnector as QueueRedisConnector;
use Pandawa\Bundle\QueueBundle\Queue\RedisQueue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisConnector extends QueueRedisConnector
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
