<?php

namespace Bytes\ResponseBundle\HttpClient\Token;

use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface TokenRefreshInterface.
 */
interface TokenRefreshInterface
{
    /**
     * Refreshes the provided access token.
     */
    public function refreshToken(AccessTokenInterface $token = null): ?AccessTokenInterface;
}
