<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Loader\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CriteriaConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('criteria');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->arrayPrototype()
                ->children()
                    ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                    ->arrayNode('arguments')->scalarPrototype()->end()->end()
                    ->arrayNode('defaults')->variablePrototype()->end()->end()
                    ->arrayNode('values')->variablePrototype()->end()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
