<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->arrayNode('middlewares')
                ->scalarPrototype()->end()
            ->end()
        ->end()
    ;

    return $rootNode;
};
