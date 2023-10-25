<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BroadcastingBundle\Listener;

use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BroadcastEvent
{
    public function __construct(private BroadcastFactory $factory)
    {
    }

    public function handle($eventName, $data): void
    {
        if (count($data) && $this->shouldBroadcast($data[0])) {
            $this->factory->queue($data[0]);
        }
    }

    protected function broadcastWhen($event): bool
    {
        return method_exists($event, 'broadcastWhen')
            ? $event->broadcastWhen() : true;
    }

    private function shouldBroadcast($event): bool
    {
        return $event instanceof ShouldBroadcast && $this->broadcastWhen($event);
    }
}
