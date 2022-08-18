<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $root = $treeBuilder->getRootNode();

    $root
        ->children()
            ->scalarNode('default')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('stores')
                ->variablePrototype()->end()
            ->end()
            ->scalarNode('prefix')->isRequired()->cannotBeEmpty()->end()
        ->end()
    ;

    return $root;
};
