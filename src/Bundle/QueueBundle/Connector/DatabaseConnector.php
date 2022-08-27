<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Connector;

use Illuminate\Queue\Connectors\DatabaseConnector as QueueDatabaseConnector;
use Pandawa\Bundle\QueueBundle\Queue\DatabaseQueue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabaseConnector extends QueueDatabaseConnector
{
    public function connect(array $config): DatabaseQueue
    {
        return new DatabaseQueue(
            $this->connections->connection($config['connection'] ?? null),
            $config['table'],
            $config['queue'],
            $config['retry_after'] ?? 60,
            $config['after_commit'] ?? null
        );
    }
}
