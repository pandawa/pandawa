<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Plugin;

use Pandawa\Annotations\Eloquent\AsObserver;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\EloquentBundle\Annotation\ObserverLoadHandler;
use Pandawa\Bundle\EloquentBundle\EloquentBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportObserverAnnotationPlugin extends AnnotationPlugin
{
    protected ?string $defaultPath = 'Observer';

    public function boot(): void
    {
        $config = $this->bundle->getService('config');

        foreach ($config->get($this->getConfigKey(), []) as $model => $observer) {
            $model::observe($observer);
        }
    }

    protected function getAnnotationClasses(): array
    {
        return [AsObserver::class];
    }

    protected function getHandler(): string
    {
        return ObserverLoadHandler::class;
    }

    protected function getConfigKey(): string
    {
        return EloquentBundle::OBSERVER_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
