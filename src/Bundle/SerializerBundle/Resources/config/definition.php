<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->scalarNode('default')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('serializers')
                ->isRequired()
                ->useAttributeAsKey('name')
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('normalizers')->scalarPrototype()->end()->end()
                        ->arrayNode('encoders')->scalarPrototype()->end()->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ;

    return $treeBuilder;
};
