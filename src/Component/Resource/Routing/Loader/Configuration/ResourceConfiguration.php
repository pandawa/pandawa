<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Loader\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ResourceConfiguration implements ConfigurationInterface
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
                ->scalarNode('name')->end()
                ->scalarNode('group')->end()
                ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('resource')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('middleware')->scalarPrototype()->end()->end()
                ->arrayNode('only')->scalarPrototype()->end()->end()
                ->arrayNode('except')->scalarPrototype()->end()->end()
                ->arrayNode('options')
                    ->children()
                        ->scalarNode('resource_key')->end()
                        ->scalarNode('default_content_type')->end()
                        ->arrayNode('index')
                            ->children()
                                ->integerNode('http_code')->defaultValue(200)->end()
                                ->integerNode('paginate')->end()
                                ->arrayNode('middleware')->scalarPrototype()->end()->end()
                                ->arrayNode('rules')->scalarPrototype()->end()->end()
                                ->append($this->addFiltersNode())
                                ->append($this->addRepositoryNode())
                                ->append($criteria->getConfigTreeBuilder()->getRootNode())
                                ->append($transformer->getConfigTreeBuilder()->getRootNode())
                                ->append($serialize->getConfigTreeBuilder()->getRootNode())
                            ->end()
                        ->end()
                        ->arrayNode('show')
                            ->children()
                                ->integerNode('http_code')->defaultValue(200)->end()
                                ->arrayNode('middleware')->scalarPrototype()->end()->end()
                                ->arrayNode('rules')->scalarPrototype()->end()->end()
                                ->append($this->addFiltersNode())
                                ->append($this->addRepositoryNode())
                                ->append($criteria->getConfigTreeBuilder()->getRootNode())
                                ->append($transformer->getConfigTreeBuilder()->getRootNode())
                                ->append($serialize->getConfigTreeBuilder()->getRootNode())
                            ->end()
                        ->end()
                        ->arrayNode('store')
                            ->children()
                                ->integerNode('http_code')->defaultValue(201)->end()
                                ->arrayNode('middleware')->scalarPrototype()->end()->end()
                                ->arrayNode('rules')->scalarPrototype()->end()->end()
                                ->append($transformer->getConfigTreeBuilder()->getRootNode())
                                ->append($serialize->getConfigTreeBuilder()->getRootNode())
                            ->end()
                        ->end()
                        ->arrayNode('update')
                            ->children()
                                ->integerNode('http_code')->defaultValue(200)->end()
                                ->arrayNode('middleware')->scalarPrototype()->end()->end()
                                ->arrayNode('rules')->scalarPrototype()->end()->end()
                                ->append($transformer->getConfigTreeBuilder()->getRootNode())
                                ->append($serialize->getConfigTreeBuilder()->getRootNode())
                            ->end()
                        ->end()
                        ->arrayNode('delete')
                            ->children()
                                ->integerNode('http_code')->defaultValue(200)->end()
                                ->arrayNode('middleware')->scalarPrototype()->end()->end()
                                ->arrayNode('rules')->scalarPrototype()->end()
                                ->append($transformer->getConfigTreeBuilder()->getRootNode())
                                ->append($serialize->getConfigTreeBuilder()->getRootNode())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    protected function addFiltersNode(): mixed
    {
        $treeBuilder = new TreeBuilder('filters');

        return $treeBuilder->getRootNode()
            ->variablePrototype()->end()
        ;
    }

    protected function addRepositoryNode(): mixed
    {
        $treeBuilder = new TreeBuilder('repository');

        return $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('call')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('arguments')->scalarPrototype()->end()->end()
            ->end()
        ;
    }
}
