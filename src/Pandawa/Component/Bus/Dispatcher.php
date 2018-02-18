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

namespace Pandawa\Component\Bus;

use Closure;
use Pandawa\Component\Message\MessageRegistryInterface;
use Pandawa\Component\Message\QueueEnvelope;
use Illuminate\Bus\Dispatcher as LaravelDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Container\Container;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Dispatcher extends LaravelDispatcher
{
    /**
     * @var MessageRegistryInterface
     */
    private $messageRegistry;

    /**
     * Constructor.
     *
     * @param Container                $container
     * @param Closure                  $queueResolver
     * @param MessageRegistryInterface $messageRegistry
     */
    public function __construct(Container $container, Closure $queueResolver = null, MessageRegistryInterface $messageRegistry)
    {
        parent::__construct($container, $queueResolver);
        $this->messageRegistry = $messageRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchNow($message, $handler = null)
    {
        if ($message instanceof QueueEnvelope) {
            $message = $message->getCommand();
        }

        return parent::dispatchNow($message, $handler);
    }

    /**
     * {@inheritdoc}
     */
    protected function commandShouldBeQueued($message): bool
    {
        return $message instanceof QueueEnvelope || $message instanceof ShouldQueue;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandHandler($command): ?object
    {
        $handlerClass = $this->getHandlerClass($command);

        if (class_exists($handlerClass)) {
            return $this->container->make($handlerClass);
        }

        return parent::getCommandHandler($command);
    }

    /**
     * Get class handler.
     *
     * @param object $message
     *
     * @return string
     */
    private function getHandlerClass(object $message): string
    {
        $messageClass = get_class($message);

        if (null !== $this->messageRegistry) {
            return $this->messageRegistry->get($messageClass)->getHandlerClass();
        }

        return sprintf('%sHandler', $messageClass);
    }
}
