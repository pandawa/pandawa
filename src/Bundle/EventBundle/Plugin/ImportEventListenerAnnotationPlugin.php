<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EventBundle\Plugin;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Event\AsListener;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\EventBundle\Annotation\ListenerLoadHandler;
use Pandawa\Bundle\EventBundle\EventBundle;
use Pandawa\Contracts\Event\EventBusInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportEventListenerAnnotationPlugin extends AnnotationPlugin
{
    protected ?string $defaultPath = 'Listener';

    public function boot(): void
    {
        foreach ($this->config()->get($this->getConfigKey()) as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventBus()->listen($event, $listener);
            }
        }
    }

    protected function getAnnotationClasses(): array
    {
        return [AsListener::class];
    }

    protected function getHandler(): string
    {
        return ListenerLoadHandler::class;
    }

    protected function eventBus(): EventBusInterface
    {
        return $this->bundle->getService(EventBusInterface::class);
    }

    protected function config(): Config
    {
        return $this->bundle->getService('config');
    }

    protected function getConfigKey(): string
    {
        return EventBundle::EVENT_CACHE_CONFIG_KEY . '.annotations.' . $this->bundle->getName();
    }
}
