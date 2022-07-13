<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    return $treeBuilder->getRootNode()
        ->children()
        ->scalarNode('name')
        ->isRequired()
        ->end()
        ->booleanNode('debug')
        ->defaultFalse()
        ->end()
        ->end();
};
