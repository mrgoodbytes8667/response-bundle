<?php

namespace Bytes\ResponseBundle\HttpClient\Token;

use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface AppTokenClientInterface.
 *
 * @experimental
 */
interface AppTokenClientInterface extends TokenClientInterface
{
    /**
     * Returns an access token.
     */
    public function getToken(): ?AccessTokenInterface;
}
