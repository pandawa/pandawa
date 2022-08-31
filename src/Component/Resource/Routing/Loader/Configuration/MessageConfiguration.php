<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Loader\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageConfiguration implements ConfigurationInterface
{
    public function __construct(protected readonly string $name)
    {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->name);

        $criteria = new CriteriaConfiguration();
        $transformer = new TransformerConfiguration();
        $serialize = new SerializeConfiguration();

        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('uri')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('name')->end()
                ->scalarNode('message')->isRequired()->cannotBeEmpty()->end()
                ->variableNode('methods')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('options')
                    ->children()
                        ->scalarNode('default_content_type')->end()
                        ->integerNode('http_code')->defaultValue(200)->end()
                        ->arrayNode('rules')->scalarPrototype()->end()->end()
                        ->append($criteria->getConfigTreeBuilder()->getRootNode())
                        ->append($transformer->getConfigTreeBuilder()->getRootNode())
                        ->append($serialize->getConfigTreeBuilder()->getRootNode())
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
