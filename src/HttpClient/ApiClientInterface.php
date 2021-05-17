<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Interface ApiClientInterface
 * @package Bytes\ResponseBundle\HttpClient
 */
interface ApiClientInterface
{
    /**
     * @param Auth|null $auth
     * @return array
     * @throws NoTokenException
     */
    public function getAuthenticationOption(?Auth $auth = null);
}