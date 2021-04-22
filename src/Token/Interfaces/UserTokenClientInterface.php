<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


/**
 * Interface UserTokenClientInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface UserTokenClientInterface extends TokenClientInterface
{
    /**
     * Validates the provided access token
     * @param AccessTokenInterface|string $token
     * @return mixed
     */
    public function validateToken(AccessTokenInterface|string $token);
}