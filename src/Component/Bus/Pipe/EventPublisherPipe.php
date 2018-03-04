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

namespace Pandawa\Component\Bus\Pipe;

use Illuminate\Contracts\Events\Dispatcher;
use Pandawa\Component\Event\EventProviderInterface;
use Pandawa\Component\Message\AbstractMessage;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class EventPublisherPipe
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var EventProviderInterface
     */
    private $eventProvider;

    /**
     * Constructor.
     *
     * @param Dispatcher             $dispatcher
     * @param EventProviderInterface $eventProvider
     */
    public function __construct(Dispatcher $dispatcher, EventProviderInterface $eventProvider)
    {
        $this->dispatcher = $dispatcher;
        $this->eventProvider = $eventProvider;
    }

    /**
     * Release pending events after message dispatched.
     *
     * @param AbstractMessage|mixed $message
     * @param mixed                 $next
     *
     * @return mixed
     */
    public function handle($message, $next)
    {
        $response = $next($message);

        foreach ($this->eventProvider->releaseEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }

        return $response;
    }
}
