<?php


namespace Bytes\ResponseBundle\Objects;


/**
 * Class ConfigNormalizer
 * @package Bytes\ResponseBundle\Objects
 */
class ConfigNormalizer
{
    /**
     * Normalizes the endpoint portion of the config, removing any indexes not in $endpoints, and adding any missing
     * indexes from $endpoints
     * @param array $config
     * @param string[] $endpoints
     * @return array
     */
    public static function normalizeEndpoints(array $config, array $endpoints)
    {
        foreach($config['endpoints'] as $index => $endpoint) {
            if(!in_array($index, $endpoints)) {
                unset($config['endpoints'][$index]);
            }
        }
        foreach ($endpoints as $index) {
            if(!isset($config['endpoints'][$index]))
            {
                $config['endpoints'][$index] = [];
            }

            if(!isset($config['endpoints'][$index]['redirects']))
            {
                $config['endpoints'][$index]['redirects'] = [];
            }
            if(!isset($config['endpoints'][$index]['redirects']['method']))
            {
                $config['endpoints'][$index]['redirects']['method'] = 'route_name';
            }
            foreach(['route_name', 'url'] as $redirects) {
                if (!isset($config['endpoints'][$index]['redirects'][$redirects])) {
                    $config['endpoints'][$index]['redirects'][$redirects] = '';
                }
            }

            foreach(['permissions', 'scopes'] as $subIndex) {
                if(!isset($config['endpoints'][$index][$subIndex])) {
                    $config['endpoints'][$index][$subIndex] = [];
                }
                foreach(['add', 'remove'] as $addRemove) {
                    if(!isset($config['endpoints'][$index][$subIndex][$addRemove])) {
                        $config['endpoints'][$index][$subIndex][$addRemove] = [];
                    }
                }
            }
        }

        return $config;
    }
}