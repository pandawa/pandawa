<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing;

use Pandawa\Contracts\Routing\LoaderInterface;
use Pandawa\Contracts\Routing\LoaderResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class LoaderResolver implements LoaderResolverInterface
{
    /**
     * @var LoaderInterface[]
     */
    protected array $loaders = [];

    /**
     * @param  LoaderInterface[]  $loaders
     */
    public function __construct(array $loaders = [])
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addLoader(LoaderInterface $loader): void
    {
        $this->loaders[] = $loader;
        $loader->setResolver($this);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(mixed $resource): ?LoaderInterface
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoaders(): array
    {
        return $this->loaders;
    }
}
