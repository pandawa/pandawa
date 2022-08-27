<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->scalarNode('default')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('connections')
                ->variablePrototype()->end()
            ->end()
            ->arrayNode('failed')
                ->children()
                    ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('database')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('table')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ->end()
    ;

    return $treeBuilder;
};
