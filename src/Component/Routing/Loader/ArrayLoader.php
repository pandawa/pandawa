<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Loader;

use Pandawa\Component\Routing\Traits\ResolverTrait;
use Pandawa\Contracts\Routing\LoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ArrayLoader implements LoaderInterface
{
    use ResolverTrait;

    public function load(mixed $resource): void
    {
        foreach ($resource as $key => $item) {
            if (is_string($key) && empty($item['name'] ?? null)) {
                $item['name'] = $key;
            }

            $this->resolver->resolve($item)?->load($item);
        }
    }

    public function supports(mixed $resource): bool
    {
        return is_array($resource) && is_array(array_first($resource));
    }
}
