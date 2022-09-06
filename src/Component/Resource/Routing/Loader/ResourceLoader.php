<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Loader;

use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pandawa\Component\Resource\Routing\Loader\Configuration\ResourceConfiguration;
use Pandawa\Component\Routing\Traits\ResolverTrait;
use Pandawa\Contracts\Routing\GroupRegistryInterface;
use Pandawa\Contracts\Routing\LoaderInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;
use ReflectionClass;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ResourceLoader implements LoaderInterface
{
    use ResolverTrait;

    const AVAILABLE_TYPES = ['index', 'show', 'store', 'update', 'delete'];
    const ROUTE_MAPS = [
        'index'  => 'get',
        'show'   => 'get',
        'store'  => 'post',
        'update' => 'patch',
        'delete' => 'delete',
    ];
    const NEEDS_ID = ['show', 'update', 'delete'];

    protected readonly Processor $processor;

    public function __construct(
        protected readonly Router $router,
        protected readonly RouteConfiguratorInterface $configurator,
        protected readonly GroupRegistryInterface $groupRegistry,
        protected readonly string $controller,
    ) {
        $this->processor = new Processor();
    }

    public function load(mixed $resource): void
    {
        $resource = $this->validate($resource);

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
        foreach ($this->getResourceTypes($resource) as $type) {
            $config = [
                ...Arr::except($resource, ['name', 'options', 'middleware']),
                'name'    => $resource['name'].'.'.$type,
                'options' => [
                    ...($resource['options'][$type] ?? []),
                    'middleware' => [
                        ...($resource['middleware'] ?? []),
                        ...($resource['options'][$type]['middleware'] ?? [])
                    ]
                ],
            ];

            $this->configurator->configure(
                $this->createRoute($type, $resource['resource'], $config['uri']),
                $config
            );
        }
    }

    public function supports(mixed $resource): bool
    {
        return is_array($resource)
            && array_key_exists('type', $resource)
            && 'resource' === $resource['type'];
    }

    protected function createRoute(string $type, string $resource, string $uri): Route
    {
        $method = self::ROUTE_MAPS[$type];

        if (in_array($type, self::NEEDS_ID)) {
            $uri = sprintf('%s/{%s}', $uri, $this->getResourceName($resource));
        }

        return $this->router->{$method}($uri, $this->controller . '@' . $type);
    }

    protected function getResourceName(string $resource): string
    {
        if (method_exists($resource, 'resourceName')) {
            return $resource::resourceName();
        }

        $reflection = new ReflectionClass($resource);

        return Str::snake(
            preg_replace('/(Model|Entity)$/', '', $reflection->getShortName())
        );
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
        $configuration = new ResourceConfiguration($name);

        return $configuration->getConfigTreeBuilder();
    }

    protected function getResourceTypes(array $resource): array
    {
        if ($only = $resource['only'] ?? null) {
            return Arr::only(self::AVAILABLE_TYPES, $only);
        }

        if ($except = $resource['except'] ?? null) {
            return Arr::except(self::AVAILABLE_TYPES, $except);
        }

        return self::AVAILABLE_TYPES;
    }

    protected function getConfigurationName(array $resource): string
    {
        return str_replace('.', '_', $resource['name']);
    }
}
