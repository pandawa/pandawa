<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AuthBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Auth\AsPolicy;
use Pandawa\Bundle\AuthBundle\AuthBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PolicyLoadHandler implements AnnotationLoadHandlerInterface
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
     * @param  array{class: ReflectionClass, annotation: AsPolicy}  $options
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

    protected function getConfigKey(): string
    {
        return AuthBundle::POLICY_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
