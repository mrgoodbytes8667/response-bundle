<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


/**
 * Interface TokenValidateInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenValidateInterface
{
    /**
     * Validates the provided access token
     * @param AccessTokenInterface $token
     * @return TokenValidationResponseInterface|null
     */
    public function validateToken(AccessTokenInterface $token): ?TokenValidationResponseInterface;
}