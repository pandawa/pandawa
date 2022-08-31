<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Loader\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TransformerConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('transformer');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('class')->end()
                ->arrayNode('context')
                    ->children()
                        ->arrayNode('available_includes')->scalarPrototype()->end()->end()
                        ->arrayNode('default_includes')->scalarPrototype()->end()->end()
                        ->arrayNode('available_selects')->scalarPrototype()->end()->end()
                        ->arrayNode('default_selects')->scalarPrototype()->end()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
