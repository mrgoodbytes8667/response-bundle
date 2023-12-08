<?php

namespace Bytes\ResponseBundle\HttpClient;

use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;

/**
 * Interface ApiClientInterface.
 */
interface ApiClientInterface
{
    /**
     * @throws NoTokenException
     */
    public function getAuthenticationOption(Auth $auth = null, bool $refresh = false): array;

    /**
     * @throws NoTokenException
     */
    public function mergeAuth(Auth $auth = null, array $options = [], bool $refresh = false, array $authHeader = null): array;
}
