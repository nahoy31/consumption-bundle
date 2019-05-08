<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Response;

use Nahoy\ApiPlatform\ConsumptionBundle\Exception\LimitExceededException;

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
                ->booleanNode('enabled_limit')->defaultTrue()->end()
                ->scalarNode('api_pattern')
                    ->defaultValue('~/api/.+~')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('cache')
                    ->defaultNull()
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
                ->arrayNode('routes_with_limit')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('method')->end()
                            ->scalarNode('pattern')->end()
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
                        ->scalarNode('user_limit')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('exception')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('status_code')
                            ->defaultValue(Response::HTTP_TOO_MANY_REQUESTS)
                            ->validate()
                            ->ifNotInArray(array_keys(Response::$statusTexts))
                                ->thenInvalid('Invalid status code "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('message')->cannotBeEmpty()->defaultValue('API rate limit exceeded for %s.')->end()
                        ->scalarNode('custom_exception')
                            ->cannotBeEmpty()
                            ->defaultNull()
                            ->validate()
                            ->ifTrue(function ($v) {
                                if (!class_exists($v)) {
                                    return true;
                                }
                                if (!is_subclass_of($v, RateLimitExceededException::class)) {
                                    return true;
                                }
                                return false;
                            })
                                ->thenInvalid('The class %s does not exist or not extend "Nahoy\ApiPlatform\ConsumptionBundle\Exception\LimitExceededException" class.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
