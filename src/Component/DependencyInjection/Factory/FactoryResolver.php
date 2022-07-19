<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection\Factory;

use Pandawa\Contracts\DependencyInjection\Factory\FactoryResolverInterface;
use Pandawa\Contracts\DependencyInjection\Factory\ServiceFactoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class FactoryResolver implements FactoryResolverInterface
{
    /**
     * @var ServiceFactoryInterface[]
     */
    protected array $factories = [];

    public function __construct(array $factories = [])
    {
        foreach ($factories as $factory) {
            $this->addFactory($factory);
        }
    }

    public function addFactory(ServiceFactoryInterface $factory): void
    {
        $this->factories[] = $factory;
    }

    public function resolve(array $config): ?ServiceFactoryInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($config)) {
                return $factory;
            }
        }

        return null;
    }
}
