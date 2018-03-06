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

use InvalidArgumentException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class EventRegistry implements EventRegistryInterface
{
    /**
     * @var array
     */
    private $events = [];

    /**
     * {@inheritdoc}
     */
    public function add(string $eventName, string $eventClass): void
    {
        $this->events[$eventName] = $eventClass;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $eventName): bool
    {
        return array_key_exists($eventName, $this->events);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $eventName): void
    {
        unset($this->events[$eventName]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $eventName): string
    {
        if (!$this->has($eventName)) {
            throw new InvalidArgumentException(sprintf('Event with name "%s" not registered.', $eventName));
        }

        return $this->events[$eventName];
    }
}
