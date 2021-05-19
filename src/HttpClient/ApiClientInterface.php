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

    /**
     * @param Auth|null $auth
     * @param array $options
     * @param bool $refresh
     * @param array|null $authHeader
     * @return array
     * @throws NoTokenException
     */
    public function mergeAuth(?Auth $auth = null, array $options = [], bool $refresh = false, array $authHeader = null): array;
}