<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder) {
    $root = $treeBuilder->getRootNode();

    $root
        ->children()
            ->scalarNode('rule_registry')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('validator_factory')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('rules')
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('constraints')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('messages')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ;

    return $treeBuilder;
};
