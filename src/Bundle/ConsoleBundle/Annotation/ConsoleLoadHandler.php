<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ConsoleBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Console\AsConsole;
use Pandawa\Bundle\ConsoleBundle\ConsoleBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ConsoleLoadHandler implements AnnotationLoadHandlerInterface
{
    protected BundleInterface $bundle;

    public function __construct(protected readonly Config $config)
    {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: AsConsole}  $options
     */
    public function handle(array $options): void
    {
        $className = $options['class']->getName();

        $this->config->set($this->getConfigKey(), [
            ...$this->config->get($this->getConfigKey(), []),
            md5($className) => $className
        ]);
    }

    protected function getConfigKey(): string
    {
        return ConsoleBundle::CONSOLE_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
