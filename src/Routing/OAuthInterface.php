<?php


namespace Bytes\ResponseBundle\Routing;


/**
 * Interface OAuthInterface
 * @package Bytes\ResponseBundle\Routing
 */
interface OAuthInterface
{
    /**
     * Get the internal redirect destination URI for OAuth
     * @return string
     */
    public function getRedirect(): string;

    /**
     * Get the external URL begin the OAuth token exchange process
     * @param string|null $state
     * @param ...$options
     * @return string
     */
    public function getAuthorizationUrl(?string $state = null, ...$options): string;
}