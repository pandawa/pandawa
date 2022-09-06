<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Loader;

use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Routing\Route;
use InvalidArgumentException;
use Pandawa\Component\Routing\Traits\ResolverTrait;
use Pandawa\Contracts\Routing\GroupRegistryInterface;
use Pandawa\Contracts\Routing\LoaderInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BasicLoader implements LoaderInterface
{
    use ResolverTrait;

    public function __construct(
        protected Router $router,
        protected RouteConfiguratorInterface $configurator,
        protected GroupRegistryInterface $groupRegistry,
    ) {
    }

    public function load(mixed $resource): void
    {
        $this->validate($resource);

        if ($group = $resource['group'] ?? null) {
            $this->router->group($this->groupRegistry->get($group), function () use ($resource) {
                $this->configure($resource);
            });

            return;
        }

        $this->configure($resource);
    }

    protected function configure(array $resource): void
    {
        $this->configurator->configure(
            $this->createRoute($resource['type'], $resource['uri'], $this->getController($resource)),
            $resource
        );
    }

    protected function validate(array $resource): void
    {
        $params = ['uri', 'controller'];

        foreach ($params as $param) {
            if (empty($resource[$param] ?? null)) {
                throw new InvalidArgumentException(sprintf('Missing "%s" in route params.', $param));
            }
        }
    }

    protected function createRoute(string $method, string $uri, string|array|callable $controller): Route
    {
        return $this->router->{$method}($uri, $controller);
    }

    protected function getController(array $resource): string|array|callable
    {
        return $resource['controller'];
    }

    public function supports(mixed $resource): bool
    {
        return is_array($resource)
            && array_key_exists('type', $resource)
            && in_array($this->getType($resource), [
                'get',
                'post',
                'put',
                'patch',
                'delete',
                'options',
            ]);
    }

    protected function getType(array $resource): string
    {
        return (string) $resource['type'];
    }
}
