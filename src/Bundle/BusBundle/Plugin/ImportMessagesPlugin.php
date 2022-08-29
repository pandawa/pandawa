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

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $this->bundle->getApp()->booted(function () {
            $config = $this->bundle->getService('config');

            foreach ($this->getMessages() as $messages) {
                $this->registry()->load($messages['messages'] ?? []);
                $this->bus()->map($messages['handlers'] ?? []);

                $config->set(BusBundle::MESSAGE_CONFIG_KEY, [
                    ...$config->get(BusBundle::MESSAGE_CONFIG_KEY, []),
                    ...($messages['messages'] ?? [])
                ]);
                $config->set(BusBundle::HANDLER_CONFIG_KEY, [
                    ...$config->get(BusBundle::HANDLER_CONFIG_KEY, []),
                    ...($messages['handlers'] ?? [])
                ]);
            }
        });
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
}
