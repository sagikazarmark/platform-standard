<?php

namespace HotfixBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
                ->arrayNode('import_export')
                    ->fixXmlConfig('processor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('processors')
                            ->useAttributeAsKey('name')
                            ->defaultValue([])
                            ->info('Provide execution context config for processor sets')
                            ->prototype('array')
                                ->children()
                                    ->arrayNode('config')
                                        ->isRequired()
                                        ->requiresAtLeastOneElement()
                                        ->useAttributeAsKey('name')
                                        ->validate()
                                            ->ifTrue(function ($v) {
                                                $invalidOptions = [
                                                    'processorAlias',
                                                    'filePath',
                                                ];

                                                foreach ($invalidOptions as $option) {
                                                    if (!empty($v[$option])) {
                                                        return true;
                                                    }
                                                }

                                                return false;
                                            })
                                            // TODO: improve message
                                            ->thenInvalid('"processorAlias", "filePath" config cannot be overridden')
                                        ->end()
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
