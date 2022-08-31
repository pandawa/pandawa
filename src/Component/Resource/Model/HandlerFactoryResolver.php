<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Model;

use Illuminate\Contracts\Container\Container;
use Pandawa\Contracts\Resource\Model\FactoryInterface;
use Pandawa\Contracts\Resource\Model\FactoryResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class HandlerFactoryResolver implements FactoryResolverInterface
{
    /**
     * @var FactoryInterface[]
     */
    protected array $factories = [];

    public function __construct(protected readonly Container $container, iterable $factories)
    {
        foreach ($factories as $factory) {
            $this->add($factory);
        }
    }

    public function add(FactoryInterface $factory): void
    {
        $this->factories[] = $factory->setContainer($this->container);
    }

    public function resolve(string $model): ?FactoryInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($model)) {
                return $factory;
            }
        }

        return null;
    }
}
