<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Routing;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface LoaderInterface
{
    public function load(mixed $resource): void;

    public function supports(mixed $resource): bool;

    public function setResolver(LoaderResolverInterface $resolver): void;
}
