<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AnnotationBundle\Handler;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Arr;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Annotation\Factory\ReaderFactoryInterface;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;
use Spiral\Attributes\ReaderInterface;

/**
 * @template TAnnotation
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class ServiceLoadHandler implements AnnotationLoadHandlerInterface
{
    protected BundleInterface $bundle;
    protected ReaderInterface $reader;

    public function __construct(
        protected readonly ServiceRegistryInterface $serviceRegistry,
        protected readonly Config $config,
        ReaderFactoryInterface $readerFactory,
    ) {
        $this->reader = $readerFactory->create();
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: TAnnotation}  $options
     */
    public function handle(array $options): void
    {
        $annotation = $options['annotation'];
        $class = $options['class'];

        $service = $this->makeServiceConfig($annotation, $class);
        $name = $service['service_name'] ?? $class->getName();

        $this->serviceRegistry->register($name, $service = Arr::except($service, ['service_name']));

        $this->mergeConfig([$name => $service]);
    }

    protected function mergeConfig(array $config): void
    {
        $this->config->set(
            $this->getConfigCacheKey(),
            [
                ...$this->config->get($this->getConfigCacheKey(), []),
                ...$config,
            ]
        );
    }

    /**
     * @param  TAnnotation  $annotation
     */
    abstract protected function makeServiceConfig(mixed $annotation, ReflectionClass $class): array;

    abstract protected function getConfigCacheKey(): string;
}
