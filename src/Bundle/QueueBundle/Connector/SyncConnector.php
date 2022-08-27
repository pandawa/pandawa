<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Connector;

use Illuminate\Queue\Connectors\SyncConnector as QueueSyncConnector;
use Pandawa\Bundle\QueueBundle\Queue\SyncQueue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SyncConnector extends QueueSyncConnector
{
    public function connect(array $config): SyncQueue
    {
        return new SyncQueue;
    }
}
