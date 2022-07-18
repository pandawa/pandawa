<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Routing;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface LoaderResolverInterface
{
    /**
     * Returns a loader able to load the resource.
     */
    public function resolve(mixed $resource): ?LoaderInterface;

    /**
     * Add loader to resolver.
     */
    public function addLoader(LoaderInterface $loader): void;

    /**
     * Returns all loader.
     *
     * @return LoaderInterface[]
     */
    public function getLoaders(): array;
}
