<?php


namespace Bytes\ResponseBundle\Objects;


/**
 * Class ConfigNormalizer
 * @package Bytes\ResponseBundle\Objects
 */
class ConfigNormalizer
{
    /**
     * Normalizes the endpoint portion of the config, removing any indexes not in $endpoints, adding any missing
     * indexes from $endpoints, then adding any missing $addRemoveParents indexes, and removing any indexes not in
     * $addRemoveParents (+redirects)
     * @param array $config
     * @param string[] $endpoints = ['app', 'bot', 'eventsub_subscribe', 'login', 'slash', user']
     * @param array $addRemoveParents = ['permissions', 'scopes']
     * @param bool $revokeOnRefresh = false
     * @param bool $fireRevokeOnRefresh = false
     * @return array
     */
    public static function normalizeEndpoints(array $config, array $endpoints, array $addRemoveParents = ['permissions', 'scopes'], bool $revokeOnRefresh = false, bool $fireRevokeOnRefresh = false)
    {
        // Remove any $config['endpoints'] not in the $endpoints argument
        foreach ($config['endpoints'] as $index => $endpoint) {
            if (!in_array($index, $endpoints)) {
                unset($config['endpoints'][$index]);
            }
        }

        foreach ($endpoints as $index) {
            if (!isset($config['endpoints'][$index])) {
                $config['endpoints'][$index] = [];
            }

            if (!isset($config['endpoints'][$index]['redirects'])) {
                $config['endpoints'][$index]['redirects'] = [];
            }
            
            if (!isset($config['endpoints'][$index]['redirects']['method'])) {
                $config['endpoints'][$index]['redirects']['method'] = 'route_name';
            }
            
            foreach (['route_name', 'url'] as $redirects) {
                if (!isset($config['endpoints'][$index]['redirects'][$redirects])) {
                    $config['endpoints'][$index]['redirects'][$redirects] = '';
                }
            }

            // Remove any $config['endpoints'] children not in the $addRemoveParents (+redirects, revoke) arguments
            $ignore = array_merge(['redirects', 'revoke_on_refresh', 'fire_revoke_on_refresh'], $addRemoveParents);
            foreach ($config['endpoints'][$index] as $subIndex => $endpoint) {
                if (!in_array($subIndex, $ignore)) {
                    unset($config['endpoints'][$index][$subIndex]);
                }
            }

            foreach ($config['endpoints'][$index] as $endpoint) {
                if(!isset($config['endpoints'][$index]['revoke_on_refresh'])) {
                    $config['endpoints'][$index]['revoke_on_refresh'] = $revokeOnRefresh;
                }
                
                if(!isset($config['endpoints'][$index]['fire_revoke_on_refresh'])) {
                    $config['endpoints'][$index]['fire_revoke_on_refresh'] = $fireRevokeOnRefresh;
                }
            }

            foreach ($addRemoveParents as $subIndex) {
                if (!isset($config['endpoints'][$index][$subIndex])) {
                    $config['endpoints'][$index][$subIndex] = [];
                }
                
                foreach (['add', 'remove'] as $addRemove) {
                    if (!isset($config['endpoints'][$index][$subIndex][$addRemove])) {
                        $config['endpoints'][$index][$subIndex][$addRemove] = [];
                    }
                }
            }
        }

        return $config;
    }
}