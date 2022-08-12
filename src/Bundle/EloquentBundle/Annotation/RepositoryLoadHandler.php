<?php

declare(strict_types=1);

namespace Pandawa\Bundle\EloquentBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Eloquent\Repository;
use Pandawa\Bundle\EloquentBundle\Plugin\ImportRepositoryAnnotationPlugin;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use Pandawa\Contracts\Eloquent\Factory\RepositoryFactoryInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RepositoryLoadHandler implements AnnotationLoadHandlerInterface
{
    protected BundleInterface $bundle;

    public function __construct(
        protected readonly ServiceRegistryInterface $serviceRegistry,
        protected readonly Config $config,
    ) {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: Repository}  $options
     */
    public function handle(array $options): void
    {
        $annotation = $options['annotation'];
        $class = $options['class'];

        $this->serviceRegistry->register($serviceClass = $class->getName(), $repo = [
            'alias'     => $annotation->alias,
            'factory'   => [
                '@' . RepositoryFactoryInterface::class,
                'create',
            ],
            'arguments' => [
                $annotation->model,
                $serviceClass,
            ],
        ]);

        $this->mergeConfig([$serviceClass => $repo]);
    }

    protected function mergeConfig(array $config): void
    {
        $this->config->set(
            $this->getConfigKey(),
            [
                ...$this->config->get($this->getConfigKey(), []),
                ...$config
            ]
        );
    }

    protected function getConfigKey(): string
    {
        return ImportRepositoryAnnotationPlugin::CACHE_KEY . '.' . $this->bundle->getName();
    }
}
