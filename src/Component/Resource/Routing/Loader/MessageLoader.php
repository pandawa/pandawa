<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Loader;

use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Routing\Route;
use Pandawa\Component\Resource\Routing\Loader\Configuration\MessageConfiguration;
use Pandawa\Component\Routing\Traits\ResolverTrait;
use Pandawa\Contracts\Routing\LoaderInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageLoader implements LoaderInterface
{
    use ResolverTrait;

    protected readonly Processor $processor;

    public function __construct(
        protected readonly Router $router,
        protected readonly RouteConfiguratorInterface $configurator,
        protected readonly string $controller,
    ) {
        $this->processor = new Processor();
    }

    public function load(mixed $resource): void
    {
        $resource = $this->validate($resource);

        $this->configurator->configure(
            $this->createRoute($resource['uri'], (array) $resource['methods']),
            $resource
        );
    }

    public function supports(mixed $resource): bool
    {
        return is_array($resource)
            && array_key_exists('type', $resource)
            && 'message' === $resource['type'];
    }

    protected function createRoute(string $uri, array $methods): Route
    {
        return $this->router->match($methods, $uri, $this->controller);
    }

    protected function validate(array $resource): array
    {
        return $this->processor->process(
            $this->createConfiguration($name = $this->getConfigurationName($resource))->buildTree(),
            [$name => $resource]
        );
    }

    protected function createConfiguration(string $name): TreeBuilder
    {
        return (new MessageConfiguration($name))->getConfigTreeBuilder();
    }

    protected function getConfigurationName(array $resource): string
    {
        return str_replace('.', '_', $resource['name']);
    }
}
