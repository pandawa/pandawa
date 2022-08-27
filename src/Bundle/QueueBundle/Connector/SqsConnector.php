<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Connector;

use Aws\Sqs\SqsClient;
use Illuminate\Queue\Connectors\SqsConnector as QueueSqsConnector;
use Illuminate\Support\Arr;
use Pandawa\Bundle\QueueBundle\Queue\SqsQueue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SqsConnector extends QueueSqsConnector
{
    public function connect(array $config): SqsQueue
    {
        $config = $this->getDefaultConfiguration($config);

        if (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return new SqsQueue(
            new SqsClient($config),
            $config['queue'],
            $config['prefix'] ?? '',
            $config['suffix'] ?? '',
            $config['after_commit'] ?? null
        );
    }

}
