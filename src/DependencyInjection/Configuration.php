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
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('client_id')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->info('The client id for the child bundle')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('client_secret')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->info('The client secret for the child bundle')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('client_public_key')
                                ->info('The client public key for the Discord bundle')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('bot_token')
                                ->info('The bot token for the Discord bundle')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('hub_secret')
                                ->info('The hub secret for the Twitch bundle')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('user_agent')
                                ->info('The user agent string for the child bundle (overrides defaults->user_agent). Format must be [Name] ([URL], [VERSION])')
                                ->defaultNull()
                            ->end()
                            ->booleanNode('user')
                                ->info('Should security be passed to the child OAuth handler?')
                                ->defaultFalse()
                            ->end()
                            ->arrayNode('endpoints')
                                //->addDefaultsIfNotSet()
                                //->children()
                                    ->useAttributeAsKey('name')
                                    ->arrayPrototype()
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
                                            ->end() // end redirects
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
                                            ->end() // end permissions
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
                                            ->end() // end scopes
                                            ->booleanNode('revokeOnRefresh')
                                                ->info('Should the existing token be revoked when refreshing a token?')
                                                ->defaultFalse()
                                            ->end() // end revokeOnRefresh
                                            ->booleanNode('fireRevokeOnRefresh')
                                                ->info('Should the existing token fire the revoke event when refreshing a token? Ignored if revokeOnRefresh is true.')
                                                ->defaultFalse()
                                            ->end() // end fireRevokeOnRefresh
                                        ->end()
                                    ->end()
                                //->end()
                            ->end()
                        ->end()
                    ->end()
                ->end() // end connections
                ->arrayNode('defaults')
                    ->children()
                        ->scalarNode('user_agent')
                            ->info('The default user agent string for the child bundles. Format must be [Name] ([URL], [VERSION])')
                            ->defaultNull()
                        ->end()
                    ->end() // end defaults children
                ->end() // end defaults
            ->end();

        return $treeBuilder;
    }
}