<?php

namespace Bytes\ResponseBundle\Routing;

/**
 * Interface OAuthInterface.
 */
interface OAuthInterface
{
    /**
     * Get the internal redirect destination URI for OAuth.
     */
    public function getRedirect(): string;

    public function getScopes(array $scopes = null): array;

    /**
     * Get the external URL begin the OAuth token exchange process.
     */
    public function getAuthorizationUrl(string $state = null, ...$options): string;

    /**
     * Return the OAuth name.
     */
    public static function getDefaultIndexName(): string;
}
