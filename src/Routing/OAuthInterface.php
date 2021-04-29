<?php


namespace Bytes\ResponseBundle\Routing;


/**
 * Interface OAuthInterface
 * @package Bytes\ResponseBundle\Routing
 */
interface OAuthInterface
{
    /**
     * @param string|null $state
     * @param ...$options
     * @return string
     */
    public function getAuthorizationUrl(?string $state = null, ...$options): string;
}