<?php

declare(strict_types=1);

namespace Pandawa\Bundle\HorizonBundle\Queue;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Str;
use Laravel\Horizon\Events\JobDeleted;
use Laravel\Horizon\Events\JobPushed;
use Laravel\Horizon\Events\JobReleased;
use Laravel\Horizon\Events\JobReserved;
use Laravel\Horizon\Events\JobsMigrated;
use Laravel\Horizon\JobPayload;
use Pandawa\Bundle\QueueBundle\Queue\RedisQueue as BaseQueue;
use Pandawa\Contracts\Event\EventBusInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisQueue extends BaseQueue
{
    protected ?object $lastPushed = null;

    public function readyNow(?string $queue = null): int
    {
        return $this->getConnection()->llen($this->getQueue($queue));
    }

    public function push($job, $data = '', $queue = null): mixed
    {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data),
            $queue,
            null,
            function ($payload, $queue) use ($job) {
                $this->lastPushed = $job;

                return $this->pushRaw($payload, $queue);
            }
        );
    }

    public function pushRaw($payload, $queue = null, array $options = []): string
    {
        $payload = (new JobPayload($payload))->prepare($this->lastPushed);

        parent::pushRaw($payload->value, $queue, $options);

        $this->event($this->getQueue($queue), new JobPushed($payload->value));

        return $payload->id();
    }

    protected function createPayloadArray($job, $queue, $data = ''): array
    {
        $payload = parent::createPayloadArray($job, $queue, $data);

        $payload['id'] = $payload['uuid'];

        return $payload;
    }

    public function later($delay, $job, $data = '', $queue = null): mixed
    {
        $payload = (new JobPayload($this->createPayload($job, $queue, $data)))->prepare($job)->value;

        if (method_exists($this, 'enqueueUsing')) {
            return $this->enqueueUsing(
                $job,
                $payload,
                $queue,
                $delay,
                function ($payload, $queue, $delay) {
                    return tap(parent::laterRaw($delay, $payload, $queue), function () use ($payload, $queue) {
                        $this->event($this->getQueue($queue), new JobPushed($payload));
                    });
                }
            );
        }

        return tap(parent::laterRaw($delay, $payload, $queue), function () use ($payload, $queue) {
            $this->event($this->getQueue($queue), new JobPushed($payload));
        });
    }

    public function pop($queue = null): ?Job
    {
        return tap(parent::pop($queue), function ($result) use ($queue) {
            if ($result) {
                $this->event($this->getQueue($queue), new JobReserved($result->getReservedJob()));
            }
        });
    }

    public function migrateExpiredJobs($from, $to): array
    {
        return tap(parent::migrateExpiredJobs($from, $to), function ($jobs) use ($to) {
            $this->event($to, new JobsMigrated($jobs));
        });
    }

    public function deleteReserved($queue, $job): void
    {
        parent::deleteReserved($queue, $job);

        $this->event($this->getQueue($queue), new JobDeleted($job, $job->getReservedJob()));
    }

    public function deleteAndRelease($queue, $job, $delay): void
    {
        parent::deleteAndRelease($queue, $job, $delay);

        $this->event($this->getQueue($queue), new JobReleased($job->getReservedJob()));
    }

    protected function event($queue, $event): void
    {
        if ($this->container && $this->container->bound(EventBusInterface::class)) {
            $queue = Str::replaceFirst('queues:', '', $queue);

            $this->container->make(EventBusInterface::class)->fire(
                $event->connection($this->getConnectionName())->queue($queue)
            );
        }
    }
}
