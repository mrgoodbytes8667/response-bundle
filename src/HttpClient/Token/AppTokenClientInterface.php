<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface AppTokenClientInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
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