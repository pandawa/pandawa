<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Bus\AsMessage;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageLoadHandler implements AnnotationLoadHandlerInterface
{
    private BundleInterface $bundle;

    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: AsMessage}  $options
     */
    public function handle(array $options): void
    {
        $annotation = $options['annotation'];
        $class = $options['class'];

        $this->config->set($this->getConfigKey(), [
            ...$this->config->get($this->getConfigKey(), []),
            $class->getName() => array_filter([
                'name'         => $annotation->name,
                'normalizer'   => $annotation->normalizer,
                'denormalizer' => $annotation->denormalizer,
                'stamps'       => $annotation->stamps,
            ])
        ]);
    }

    protected function getConfigKey(): string
    {
        return BusBundle::PANDAWA_MESSAGE_CONFIG_KEY . '.' . $this->bundle->getName() . '.annotations';
    }
}
