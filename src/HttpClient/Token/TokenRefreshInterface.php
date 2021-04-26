<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface TokenRefreshInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
 */
interface TokenRefreshInterface
{
    /**
     * Refreshes the provided access token
     * @param AccessTokenInterface|null $token
     * @return AccessTokenInterface|null
     */
    public function refreshToken(AccessTokenInterface $token = null): ?AccessTokenInterface;
}