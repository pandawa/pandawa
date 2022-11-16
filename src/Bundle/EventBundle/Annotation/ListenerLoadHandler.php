<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EventBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Arr;
use Pandawa\Annotations\Event\AsListener;
use Pandawa\Bundle\EventBundle\EventBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ListenerLoadHandler implements AnnotationLoadHandlerInterface
{
    private BundleInterface $bundle;

    public function __construct(private readonly Config $config)
    {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: AsListener}  $options
     *
     * @return void
     */
    public function handle(array $options): void
    {
        $annotation = $options['annotation'];
        $class = $options['class'];
        $events = Arr::wrap($annotation->event);
        $current = $this->config->get($this->getConfigKey(), []);
        $eventListeners = [];

        foreach ($events as $event) {
            $eventListeners[$event] = [
                ...($current[$event] ?? []),
                $class->getName()
            ];
        }

        $this->config->set($this->getConfigKey(), [
            ...$current,
            ...$eventListeners
        ]);
    }

    private function getConfigKey(): string
    {
        return EventBundle::EVENT_CACHE_CONFIG_KEY . '.annotations.' . $this->bundle->getName();
    }
}
