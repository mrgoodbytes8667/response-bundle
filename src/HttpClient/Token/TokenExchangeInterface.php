<?php

namespace Bytes\ResponseBundle\HttpClient\Token;

use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Interface TokenExchangeInterface.
 *
 * @experimental
 */
interface TokenExchangeInterface
{
    /**
     * Exchanges the provided code for a new access token.
     *
     * @param string|null                         $route             Either $route or $url is required, $route takes precedence over $url
     * @param string|callable(string, array)|null $url               Either $route or $url is required, $route takes precedence over $url
     * @param callable(static, mixed)|null        $onSuccessCallable If set, will be triggered if it returns successfully
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function exchange(string $code, string $route = null, string|callable $url = null, array $scopes = [], callable $onSuccessCallable = null): ?AccessTokenInterface;
}
