<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenClientInterface extends TokenExchangeInterface, TokenRevokeInterface, TokenValidateInterface
{
    /**
     * Refreshes the provided access token
     * @param AccessTokenInterface|null $token
     * @return AccessTokenInterface|null
     */
    public function refreshToken(AccessTokenInterface $token = null): ?AccessTokenInterface;
}