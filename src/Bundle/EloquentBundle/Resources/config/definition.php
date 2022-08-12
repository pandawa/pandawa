<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

return static function (TreeBuilder $treeBuilder): TreeBuilder {
    $root = $treeBuilder->getRootNode();

    $root
        ->children()
            ->arrayNode('persistent')
                ->children()
                    ->scalarNode('class')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('middlewares')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('factory')
                ->children()
                    ->scalarNode('cache')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('query_builder')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('repository')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('cache')
                ->children()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->scalarNode('store')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('ttl')->defaultValue(60 * 60 * 24)->end()
                    ->scalarNode('handler')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('annotation')
                ->children()
                    ->scalarNode('handler')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ->end();

    return $treeBuilder;
};
