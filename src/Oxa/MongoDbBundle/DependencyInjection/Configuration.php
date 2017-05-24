<?php

namespace Oxa\MongoDbBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mongo_db')
            ->children()
            ->scalarNode('db')->end()
            ->scalarNode('host')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
