<?php

namespace Bytes\ResponseBundle\HttpClient\Token;

use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use Bytes\ResponseBundle\Token\Exceptions\TokenRevokeException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface TokenRevokeInterface.
 *
 * @experimental
 */
interface TokenRevokeInterface
{
    /**
     * Revokes the provided access token.
     *
     * @throws TokenRevokeException
     */
    public function revokeToken(AccessTokenInterface $token): ClientResponseInterface;
}
