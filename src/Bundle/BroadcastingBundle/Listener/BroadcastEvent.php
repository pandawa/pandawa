<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BroadcastingBundle\Listener;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BroadcastEvent
{
    public function __construct(private BroadcastFactory|BroadcastManager $factory)
    {
    }

    public function handle($event): void
    {
        if ($this->shouldBroadcast($event)) {
            $this->factory->queue($event);
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
