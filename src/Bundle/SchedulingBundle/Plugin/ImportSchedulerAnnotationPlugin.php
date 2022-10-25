<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SchedulingBundle\Plugin;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\Container;
use Pandawa\Annotations\Scheduling\AsScheduler;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\SchedulingBundle\Annotation\SchedulerLoadHandler;
use Pandawa\Bundle\SchedulingBundle\Contract\SchedulerInterface;
use Pandawa\Bundle\SchedulingBundle\SchedulingBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportSchedulerAnnotationPlugin extends AnnotationPlugin
{
    protected ?string $targetClass = SchedulerInterface::class;
    protected ?string $defaultPath = 'Scheduler';

    public function boot(): void
    {
        $this->bundle->getApp()->afterResolving(Schedule::class, function (Schedule $schedule, Container $container) {
            $config = $this->bundle->getService('config');

            foreach ($config->get($this->getConfigKey(), []) as $scheduler => $option) {
                $scheduler = $container->make($scheduler);

                if ($scheduler instanceof SchedulerInterface) {
                    $scheduler->schedule($schedule);
                }
            }
        });
    }

    protected function getAnnotationClasses(): array
    {
        return [AsScheduler::class];
    }

    protected function getHandler(): string
    {
        return SchedulerLoadHandler::class;
    }

    protected function getConfigKey(): string
    {
        return SchedulingBundle::SCHEDULER_CONFIG_KEY.'.'.$this->bundle->getName().'.annotations';
    }
}
