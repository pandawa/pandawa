<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $root = $treeBuilder->getRootNode();

    $root
        ->children()
            ->arrayNode('readers')
                ->scalarPrototype()->end()
            ->end()
        ->end()
    ;
};
