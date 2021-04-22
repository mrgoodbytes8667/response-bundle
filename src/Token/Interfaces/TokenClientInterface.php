<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;

/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenClientInterface extends TokenExchangeInterface
{
    /**
     * Revokes the provided access token
     * @param AccessTokenInterface|string $token
     * @return ClientResponseInterface
     */
    public function revokeToken(AccessTokenInterface|string $token): ClientResponseInterface;

    /**
     * Refreshes the provided access token
     * @param AccessTokenInterface|null $token
     * @return AccessTokenInterface|null
     */
    public function refreshToken(AccessTokenInterface $token = null): ?AccessTokenInterface;
}