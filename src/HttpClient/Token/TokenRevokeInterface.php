<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use Bytes\ResponseBundle\Token\Exceptions\TokenRevokeException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface TokenRevokeInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
 *
 * @experimental
 */
interface TokenRevokeInterface
{
    /**
     * Revokes the provided access token
     * @param AccessTokenInterface $token
     * @return ClientResponseInterface
     *
     * @throws TokenRevokeException
     */
    public function revokeToken(AccessTokenInterface $token): ClientResponseInterface;
}