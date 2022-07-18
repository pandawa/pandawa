<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Loader;

use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Pandawa\Component\Routing\Traits\ResolverTrait;
use Pandawa\Contracts\Routing\LoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class GroupLoader implements LoaderInterface
{
    use ResolverTrait;

    public function __construct(protected Router $router)
    {
    }

    public function load(mixed $resource): void
    {
        $this->validate($resource);

        $this->router->group(
            Arr::only($resource, ['middleware', 'namespace', 'prefix', 'where']),
            $this->loadChildren($resource['children'])
        );
    }

    protected function validate(array $resource): void
    {
        if (empty($children = $resource['children'] ?? null)) {
            throw new InvalidArgumentException('Missing "children" param in route group.');
        }

        if (!is_array($children) && !is_string($children)) {
            throw new InvalidArgumentException('Route param "children" should be array or string.');
        }

        if (is_array($children) && !is_array(array_first($children))) {
            throw new InvalidArgumentException('Route param "children" should be array list.');
        }

        if (is_string($children) && !file_exists($children)) {
            throw new InvalidArgumentException(sprintf('File "%s" in route children is not found.', $children));
        }
    }

    protected function loadChildren(string|array $children): callable
    {
        return function () use ($children) {
            $this->resolver->resolve($children)?->load($children);
        };
    }

    public function supports(mixed $resource): bool
    {
        return is_array($resource)
            && array_key_exists('type', $resource)
            && 'group' === $resource['type'];
    }
}
