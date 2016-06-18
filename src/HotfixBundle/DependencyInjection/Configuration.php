<?php

namespace HotfixBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hotfix');

        $this->addWsse($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addWsse(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('wsse')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('clock_skew')
                            ->cannotBeEmpty()
                            ->defaultValue(0)
                            ->info('An amount of seconds to tolerate differences between client and server')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
