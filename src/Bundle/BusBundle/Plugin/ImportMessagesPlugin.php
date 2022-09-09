<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Plugin;

use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Config\LoaderInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportMessagesPlugin extends Plugin
{
    public function __construct(protected string $basePath = 'Resources/messages')
    {
    }

    public function boot(): void
    {
        $config = $this->bundle->getService('config');

        $this->registry()->load(
            $config->get($this->getMessageConfigKey())
        );

        $this->bus()->map(
            $config->get($this->getHandlerConfigKey())
        );
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $config = $this->bundle->getService('config');

        foreach ($this->getMessages() as $messages) {
            $config->set($this->getMessageConfigKey(), [
                ...$config->get($this->getMessageConfigKey(), []),
                ...($messages['messages'] ?? [])
            ]);
            $config->set($this->getHandlerConfigKey(), [
                ...$config->get($this->getHandlerConfigKey(), []),
                ...($messages['handlers'] ?? [])
            ]);
        }
    }

    protected function getMessages(): iterable
    {
        foreach (Finder::create()->in($this->bundle->getPath($this->basePath))->files() as $file) {
            yield $this->loader()->load($file->getRealPath());
        }
    }

    protected function registry(): RegistryInterface
    {
        return $this->bundle->getService(RegistryInterface::class);
    }

    protected function loader(): LoaderInterface
    {
        return $this->bundle->getService(LoaderInterface::class);
    }

    protected function bus(): BusInterface
    {
        return $this->bundle->getService(BusInterface::class);
    }

    protected function getMessageConfigKey(): string
    {
        return BusBundle::PANDAWA_MESSAGE_CONFIG_KEY . '.' . $this->bundle->getName() . '.registries';
    }

    protected function getHandlerConfigKey(): string
    {
        return BusBundle::PANDAWA_HANDLER_CONFIG_KEY . '.' . $this->bundle->getName() . '.registries';
    }
}
