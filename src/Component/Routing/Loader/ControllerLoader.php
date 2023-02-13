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
final class ControllerLoader implements LoaderInterface
{
    use ResolverTrait;

    public function __construct(
        private readonly Router $router,
        private readonly RouteConfiguratorInterface $configurator,
        private readonly GroupRegistryInterface $groupRegistry,
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

    public function supports(mixed $resource): bool
    {
        return is_array($resource)
            && array_key_exists('controller', $resource)
            && array_key_exists('methods', $resource);
    }

    private function validate(array $resource): void
    {
        $params = ['uri', 'controller'];

        foreach ($params as $param) {
            if (empty($resource[$param] ?? null)) {
                throw new InvalidArgumentException(sprintf('Missing "%s" in route params.', $param));
            }
        }
    }

    private function configure(array $resource): void
    {
        $this->configurator->configure(
            $this->createRoute($resource['uri'], $resource['methods'], $resource['controller']),
            $resource
        );
    }

    private function createRoute(string $uri, string|array $methods, string|array|callable $controller): Route
    {
        return $this->router->match($methods, $uri, $controller);
    }
}
