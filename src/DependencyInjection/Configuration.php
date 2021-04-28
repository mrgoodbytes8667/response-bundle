<?php


namespace Bytes\ResponseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Bytes\ResponseBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('bytes_response');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('bundles')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('client_id')->defaultValue('')->end()
                            ->scalarNode('client_secret')->defaultValue('')->end()
                            ->scalarNode('client_public_key')->defaultValue('')->end()
                            ->scalarNode('bot_token')->defaultValue('')->end()
                            ->scalarNode('hub_secret')->defaultValue('')->end()
                            ->scalarNode('user_agent')->defaultNull()->end()
                            ->booleanNode('user')->defaultFalse()->end()
                            ->arrayNode('endpoints')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->arrayNode('bot')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('redirects')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->enumNode('method')
                                                        ->values(['route_name', 'url'])
                                                        ->defaultValue('route_name')
                                                    ->end()
                                                    ->scalarNode('route_name')->defaultValue('')->end()
                                                    ->scalarNode('url')->defaultValue('')->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('permissions')
                                                ->addDefaultsIfNotSet()
                                                ->info('String constants from the Permissions enum class')
                                                ->children()
                                                    ->arrayNode('add')
                                                        ->scalarPrototype()
                                                            //->beforeNormalization()
                                                            //    ->always()
                                                            //    ->then(function ($v) { return (new Permissions($v))->value; })
                                                            //->end()
                                                        ->end()
                                                    ->end()
                                                    ->arrayNode('remove')
                                                        ->scalarPrototype()
                                                            //->beforeNormalization()
                                                            //    ->always()
                                                            //    ->then(function ($v) { return (new Permissions($v))->value; })
                                                            //->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('scopes')
                                                ->addDefaultsIfNotSet()
                                                ->info('String constants from the OAuthScopes enum class')
                                                ->children()
                                                    ->arrayNode('add')
                                                        ->scalarPrototype()
                                                            //->beforeNormalization()
                                                            //    ->always()
                                                            //    ->then(function ($v) { return (new OAuthScopes($v))->value; })
                                                            //->end()
                                                        ->end()
                                                    ->end()
                                                    ->arrayNode('remove')
                                                        ->scalarPrototype()
                                                            //->beforeNormalization()
                                                            //    ->always()
                                                            //    ->then(function ($v) { return (new OAuthScopes($v))->value; })
                                                            //->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                     // repeat for bot, login, slash, user
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}