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

namespace Pandawa\Component\Module\Provider;

use Illuminate\Contracts\Events\Dispatcher;
use Pandawa\Component\Event\EventRegistryInterface;
use Pandawa\Component\Event\NameableEventInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait EventProviderTrait
{
    /**
     * @var string
     */
    protected $eventPath = 'Event';

    protected function bootEventProvider(): void
    {
        if (null !== $this->eventRegistry()) {
            $basePath = $this->getCurrentPath() . '/' . trim($this->eventPath, '/');

            if (is_dir($basePath)) {
                foreach (Finder::create()->in($basePath)->files() as $file) {
                    $eventClass = $this->getClassFromFile($file);

                    if (in_array(NameableEventInterface::class, class_implements($eventClass), true)) {
                        $this->eventRegistry()->add($eventClass::{'name'}(), $eventClass);
                    }
                }
            }
        }

        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventListener()->listen($event, $listener);
            }
        }
    }

    abstract protected function listens(): array;

    protected function eventListener(): Dispatcher
    {
        return app(Dispatcher::class);
    }

    protected function eventRegistry(): ?EventRegistryInterface
    {
        if (app()->has(EventRegistryInterface::class)) {
            return app(EventRegistryInterface::class);
        }

        return null;
    }
}
