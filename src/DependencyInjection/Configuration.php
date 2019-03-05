<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nahoy_api_platform_consumption');

        $rootNode
            ->children()
                ->scalarNode('api_pattern')
                    ->defaultValue('~/api/.+~')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('class')
                    ->isRequired()
                    ->children()
                        ->scalarNode('consumption')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('user')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('getter')
                    ->isRequired()
                    ->children()
                        ->scalarNode('user_id')
                            ->defaultValue('id')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('user_username')
                            ->defaultValue('username')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
