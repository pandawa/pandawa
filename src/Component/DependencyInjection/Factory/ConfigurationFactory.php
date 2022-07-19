<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection\Factory;

use Pandawa\Contracts\DependencyInjection\Factory\ConfigurationFactoryInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ConfigurationFactory implements ConfigurationFactoryInterface
{
    public function create(string $name): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($name);

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('class')
            ->cannotBeEmpty()
            ->end()
            ->variableNode('factory')
            ->cannotBeEmpty()
            ->end()
            ->arrayNode('arguments')
            ->scalarPrototype()->end()
            ->end()
            ->booleanNode('shared')
            ->defaultTrue()
            ->end()
            ->variableNode('alias')->end()
            ->scalarNode('tag')->end()
            ->end();

        return $treeBuilder;
    }
}
