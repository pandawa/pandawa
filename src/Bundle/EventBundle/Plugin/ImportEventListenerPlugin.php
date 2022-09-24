<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EventBundle\Plugin;

use Illuminate\Contracts\Config\Repository;
use Pandawa\Bundle\EventBundle\EventBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\Event\EventBusInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportEventListenerPlugin extends Plugin
{
    public function __construct(protected readonly string $path = 'Resources/listeners')
    {
    }

    public function boot(): void
    {
        foreach ($this->config()->get($this->getConfigKey(), []) as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventBus()->listen($event, $listener);
            }
        }
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        foreach ($this->getListeners() as $listeners) {
            $this->config()->set($this->getConfigKey(), [
                ...$this->config()->get($this->getConfigKey(), []),
                ...($listeners ?? [])
            ]);
        }
    }

    protected function eventBus(): EventBusInterface
    {
        return $this->bundle->getService(EventBusInterface::class);
    }

    protected function config(): Repository
    {
        return $this->bundle->getService('config');
    }

    protected function getListeners(): iterable
    {
        foreach (Finder::create()->in($this->bundle->getPath($this->path))->files() as $file) {
            yield $this->loader()->load($file->getRealPath());
        }
    }

    protected function loader(): LoaderInterface
    {
        return $this->bundle->getService(LoaderInterface::class);
    }

    protected function getConfigKey(): string
    {
        return EventBundle::EVENT_CACHE_CONFIG_KEY . '.resource.' . $this->bundle->getName();
    }
}
