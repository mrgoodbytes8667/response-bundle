<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;

/**
 * Interface TokenRevokeInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenRevokeInterface
{
    /**
     * Revokes the provided access token
     * @param AccessTokenInterface $token
     * @return ClientResponseInterface
     */
    public function revokeToken(AccessTokenInterface $token): ClientResponseInterface;
}