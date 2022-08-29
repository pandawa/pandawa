<?php

declare(strict_types=1);

namespace Pandawa\Bundle\BusBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Bus\Message;
use Pandawa\Bundle\BusBundle\BusBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageLoadHandler implements AnnotationLoadHandlerInterface
{
    private BundleInterface $bundle;

    public function __construct(
        private readonly RegistryInterface $messageRegistry,
        private readonly Config $config,
    ) {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: Message}  $options
     *
     * @return void
     */
    public function handle(array $options): void
    {
        $annotation = $options['annotation'];
        $class = $options['class'];

        $this->messageRegistry->add(
            $messageClass = $class->getName(),
            $message = array_filter([
                'name'         => $annotation->name,
                'normalizer'   => $annotation->normalizer,
                'denormalizer' => $annotation->denormalizer,
                'stamps'       => $annotation->stamps,
            ])
        );

        $this->config->set(BusBundle::MESSAGE_CONFIG_KEY, [
            ...$this->config->get(BusBundle::MESSAGE_CONFIG_KEY, []),
            $messageClass => $message
        ]);
    }
}
