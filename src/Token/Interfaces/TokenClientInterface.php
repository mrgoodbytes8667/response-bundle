<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


use Bytes\ResponseBundle\Enums\OAuthGrantTypes;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenClientInterface
{
    /**
     * Returns an access token
     * @return AccessTokenInterface|null
     */
    public function getToken(): ?AccessTokenInterface;

    /**
     * Revokes the provided access token
     * @param AccessTokenInterface|string $token
     * @return ResponseInterface
     */
    public function revokeToken(AccessTokenInterface|string $token);

    /**
     * Validates the provided access token
     * @param AccessTokenInterface $token
     * @return mixed
     */
    public function validateToken(AccessTokenInterface $token);

    /**
     * Exchanges the provided code for an access token
     * @param string $code
     * @param string $redirect
     * @param array $scopes
     * @param OAuthGrantTypes|null $grantType
     * @return AccessTokenInterface|null
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function tokenExchange(string $code, string $redirect, array $scopes = [], OAuthGrantTypes $grantType = null): ?AccessTokenInterface;

    /**
     * Refreshes the provided access token
     * @param AccessTokenInterface|null $token
     * @return AccessTokenInterface|null
     */
    public function refreshToken(AccessTokenInterface $token = null): ?AccessTokenInterface;
}