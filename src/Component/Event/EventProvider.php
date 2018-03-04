<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class EventProvider implements EventProviderInterface
{
    /**
     * @var array
     */
    private $unreleasedEvents = [];

    /**
     * Queue event to un-release events.
     *
     * @param object $event
     */
    public function raise(object $event): void
    {
        $this->unreleasedEvents[] = $event;
    }

    /**
     * Release all events and flush it.
     *
     * @return array|object[]
     */
    public function releaseEvents(): array
    {
        $events = $this->unreleasedEvents;
        $this->unreleasedEvents = [];

        return $events;
    }
}
