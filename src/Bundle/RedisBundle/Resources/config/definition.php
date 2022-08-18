<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $root = $treeBuilder->getRootNode();

    $root
        ->children()
            ->scalarNode('default')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('client')->defaultValue('phpredis')->cannotBeEmpty()->end()
            ->arrayNode('options')
                ->children()
                    ->scalarNode('cluster')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('prefix')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('connections')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('url')->end()
                        ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('port')->defaultValue('6379')->end()
                        ->scalarNode('database')->defaultValue('0')->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ;

    return $root;
};
