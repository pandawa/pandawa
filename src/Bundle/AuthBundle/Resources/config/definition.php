<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->arrayNode('defaults')
                ->children()
                    ->scalarNode('guard')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('passwords')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('guards')
                ->useAttributeAsKey('name')
                ->variablePrototype()->end()
            ->end()
            ->arrayNode('providers')
                ->useAttributeAsKey('name')
                ->variablePrototype()->end()
            ->end()
            ->arrayNode('passwords')
                ->useAttributeAsKey('name')
                ->variablePrototype()->end()
            ->end()
            ->integerNode('password_timeout')->isRequired()->end()
            ->arrayNode('policies')
                ->useAttributeAsKey('name')
                ->scalarPrototype()->end()
            ->end()
        ->end()
    ;

    return $treeBuilder;
};
