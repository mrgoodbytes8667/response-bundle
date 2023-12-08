<?php

namespace Bytes\ResponseBundle\HttpClient\Token;

use Bytes\ResponseBundle\Event\TokenValidatedEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;

/**
 * Interface TokenValidateInterface.
 *
 * @experimental
 */
interface TokenValidateInterface
{
    /**
     * Validates the provided access token
     * Should fire a TokenValidatedEvent on success if $fireCallback is true.
     *
     * @param bool $fireCallback Should a TokenValidatedEvent be fired?
     *
     * @see TokenValidatedEvent
     */
    public function validateToken(AccessTokenInterface $token, bool $fireCallback = false): ?TokenValidationResponseInterface;
}
