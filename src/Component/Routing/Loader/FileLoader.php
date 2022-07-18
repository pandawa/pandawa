<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Loader;

use InvalidArgumentException;
use Pandawa\Component\Routing\Traits\ResolverTrait;
use Pandawa\Contracts\Config\LoaderInterface as ConfigLoaderInterface;
use Pandawa\Contracts\Routing\LoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class FileLoader implements LoaderInterface
{
    use ResolverTrait;

    public function __construct(protected ConfigLoaderInterface $configLoader)
    {
    }

    public function load(mixed $resource): void
    {
        if (!$this->configLoader->supports($resource)) {
            throw new InvalidArgumentException(sprintf('Unsupported load resource "%s".', $resource));
        }

        $basePath = dirname($resource);

        tap($this->configLoader->load($resource), function (array $resource) use ($basePath) {
            $this->resolver->resolve($resource)?->load(
                $this->addBasePath($resource, $basePath)
            );
        });
    }

    public function supports(mixed $resource): bool
    {
        return is_string($resource) && file_exists($resource);
    }

    protected function addBasePath(array $resources, string $basePath): array
    {
        if (is_array(array_first($resources))) {
            foreach ($resources as $key => $resource) {
                $resources[$key] = $this->addBasePath($resource, $basePath);
            }
        }

        if (!empty($children = $resources['children'] ?? null) && !preg_match('/^\//', $children)) {
            $resources['children'] = rtrim($basePath, '/').'/'.$children;
        }

        return $resources;
    }
}
