<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RoutingBundle\Annotation;

use Illuminate\Contracts\Config\Repository as Config;
use Pandawa\Annotations\Routing\AsMiddleware;
use Pandawa\Bundle\RoutingBundle\RoutingBundle;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Foundation\BundleInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MiddlewareLoadHandler implements AnnotationLoadHandlerInterface
{
    protected BundleInterface $bundle;

    public function __construct(
        protected readonly Config $config,
    ) {
    }

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @param  array{class: ReflectionClass, annotation: AsMiddleware}  $options
     */
    public function handle(array $options): void
    {
        $class = $options['class'];
        $annotation = $options['annotation'];

        $this->config->set($this->getAliasConfigKey(), [
            ...$this->config->get($this->getAliasConfigKey(), []),
            $annotation->name => $class->getName(),
        ]);

        if ($group = $annotation->group) {
            $this->config->set($this->getGroupsConfigKey($group), [
                ...$this->config->get($this->getGroupsConfigKey($group), []),
                $annotation->name
            ]);
        }
    }

    protected function getAliasConfigKey(): string
    {
        return RoutingBundle::MIDDLEWARE_ALIASES_CONFIG_KEY . '.' . $this->bundle->getName() . '.aliases';
    }

    protected function getGroupsConfigKey(string $group): string
    {
        return RoutingBundle::MIDDLEWARE_GROUPS_CONFIG_KEY . '.' . $this->bundle->getName() . '.groups.' . $group;
    }
}
