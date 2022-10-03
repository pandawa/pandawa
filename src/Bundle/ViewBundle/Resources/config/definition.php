<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->arrayNode('paths')
                ->scalarPrototype()->end()
            ->end()
            ->scalarNode('compiled')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
        ->end()
    ;
};
