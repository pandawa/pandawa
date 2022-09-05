<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ConsoleBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Bundle\ConsoleBundle\ConsoleBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;

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
     * @param  array{class: ReflectionClass, annotation: AsCommand}  $options
     */
    public function handle(array $options): void
    {
        $this->config->set($this->getConsoleConfigKey(), [
            ...$this->config->get($this->getConsoleConfigKey(), []),
            $options['annotation']->name => $options['class']->getName()
        ]);
    }

    protected function getConsoleConfigKey(): string
    {
        return ConsoleBundle::CONSOLE_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
