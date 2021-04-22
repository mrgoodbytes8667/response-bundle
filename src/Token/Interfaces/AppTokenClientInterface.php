<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


/**
 * Interface AppTokenClientInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface AppTokenClientInterface extends TokenClientInterface
{
    /**
     * Returns an access token
     * @return AccessTokenInterface|null
     */
    public function getToken(): ?AccessTokenInterface;
}