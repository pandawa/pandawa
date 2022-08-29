<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Bus\Handler;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageHandlerLoadHandler implements AnnotationLoadHandlerInterface
{
    private BundleInterface $bundle;

    public function __construct(
        private readonly BusInterface $bus,
        private readonly Config $config,
    ) {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: Handler}  $options
     *
     * @return void
     */
    public function handle(array $options): void
    {
        $annotation = $options['annotation'];
        $class = $options['class'];

        $this->bus->map($map = [ $annotation->message => $class->getName() ]);

        $this->config->set(BusBundle::HANDLER_CONFIG_KEY, [
            ...$this->config->get(BusBundle::HANDLER_CONFIG_KEY, []),
            ...$map
        ]);
    }
}
