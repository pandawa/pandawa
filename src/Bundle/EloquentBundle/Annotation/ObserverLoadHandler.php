<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Eloquent\AsObserver;
use Pandawa\Bundle\EloquentBundle\EloquentBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ObserverLoadHandler implements AnnotationLoadHandlerInterface
{
    private BundleInterface $bundle;

    public function __construct(protected readonly Config $config)
    {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: AsObserver}  $options
     *
     * @return void
     */
    public function handle(array $options): void
    {
        $policy = $options['class'];
        $annotation = $options['annotation'];

        $this->config->set($this->getConfigKey(), [
            ...$this->config->get($this->getConfigKey(), []),
            $annotation->model => $policy->getName(),
        ]);
    }

    private function getConfigKey(): string
    {
        return EloquentBundle::OBSERVER_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
