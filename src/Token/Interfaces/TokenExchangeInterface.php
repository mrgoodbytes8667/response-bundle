<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


use Bytes\ResponseBundle\Enums\OAuthGrantTypes;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Interface TokenExchangeInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenExchangeInterface
{
    /**
     * Exchanges the provided code (or token) for a (new) access token
     * @param string $code
     * @param string|null $route Either $route or $url is required, $route takes precedence over $url
     * @param string|null|callable(string, array) $url Either $route or $url is required, $route takes precedence over $url
     * @param array $scopes
     * @param OAuthGrantTypes|null $grantType
     * @param callable(static, mixed)|null $onSuccessCallable If set, will be triggered if it returns successfully
     * @return AccessTokenInterface|null
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function tokenExchange(string $code, ?string $route = null, string|callable|null $url = null, array $scopes = [], OAuthGrantTypes $grantType = null, ?callable $onSuccessCallable = null): ?AccessTokenInterface;
}