<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidateInterface;

/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
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