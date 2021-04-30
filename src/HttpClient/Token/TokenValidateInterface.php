<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Event\TokenValidatedEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;

/**
 * Interface TokenValidateInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
 *
 * @experimental
 */
interface TokenValidateInterface
{
    /**
     * Validates the provided access token
     * Should fire a TokenValidatedEvent on success
     * @param AccessTokenInterface $token
     * @return TokenValidationResponseInterface|null
     *
     * @see TokenValidatedEvent
     */
    public function validateToken(AccessTokenInterface $token): ?TokenValidationResponseInterface;
}