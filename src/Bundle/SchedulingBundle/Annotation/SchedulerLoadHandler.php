<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SchedulingBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Scheduling\AsScheduler;
use Pandawa\Bundle\SchedulingBundle\SchedulingBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SchedulerLoadHandler implements AnnotationLoadHandlerInterface
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
     * @param  array{class: ReflectionClass, annotation: AsScheduler}  $options
     *
     * @return void
     */
    public function handle(array $options): void
    {
        $class = $options['class'];

        $this->config->set($this->getConfigKey(), [
            ...$this->config->get($this->getConfigKey(), []),
            $class->getName() => true,
        ]);
    }

    private function getConfigKey(): string
    {
        return SchedulingBundle::SCHEDULER_CONFIG_KEY.'.'.$this->bundle->getName().'.annotations';
    }
}
