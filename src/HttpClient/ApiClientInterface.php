<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;

/**
 * Interface ApiClientInterface
 * @package Bytes\ResponseBundle\HttpClient
 */
interface ApiClientInterface
{
    /**
     * @param Auth|null $auth
     * @param bool $refresh
     * @return array
     * @throws NoTokenException
     */
    public function getAuthenticationOption(?Auth $auth = null, bool $refresh = false): array;
}