<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->arrayNode('middlewares')
                ->scalarPrototype()->end()
            ->end()
            ->arrayNode('handlers')
                ->scalarPrototype()->end()
            ->end()
            ->scalarNode('registry')->end()
            ->scalarNode('queue_factory')->end()
            ->scalarNode('message_bus')->end()
            ->arrayNode('annotation')
                ->children()
                    ->scalarNode('message_handler')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('message_handler_handler')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('messages')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('stamps')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                                    ->arrayNode('arguments')
                                        ->scalarPrototype()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ;

    return $rootNode;
};
