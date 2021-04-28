<?php


namespace Bytes\ResponseBundle\Services;


/**
 * Interface OAuthInterface
 * @package Bytes\ResponseBundle\Services
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