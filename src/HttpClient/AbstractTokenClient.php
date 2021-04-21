<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Enums\HttpMethods;
use Bytes\ResponseBundle\Enums\OAuthGrantTypes;
use Bytes\ResponseBundle\Objects\Push;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenClientInterface;
use Illuminate\Support\Arr;
use LogicException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractTokenClient
 * @package Bytes\ResponseBundle\HttpClient
 *
 * @experimental
 */
abstract class AbstractTokenClient extends AbstractClient implements TokenClientInterface
{
    /**
     * @var string|null
     */
    protected static $tokenExchangeBaseUri;
    /**
     * @var string
     */
    protected static $tokenExchangeEndpoint = 'oauth2/token';

    /**
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
    public function tokenExchange(string $code, string $redirect, array $scopes = [], OAuthGrantTypes $grantType = null): ?AccessTokenInterface
    {
        $body = Push::createPush(value: empty($grantType) ? OAuthGrantTypes::authorizationCode()->value : $grantType->value, key: 'grant_type')
            ->push($redirect, 'redirect_uri')
            ->push(static::buildOAuthString($scopes), 'scope');

        $body = match ($grantType) {
            OAuthGrantTypes::authorizationCode() => $body->push($code, 'code'),
            OAuthGrantTypes::refreshToken() => $body->push($code, 'refresh_token'),
        };

        return $this->request($this->buildURL(static::getTokenExchangeBaseUri() . static::$tokenExchangeEndpoint),
            AccessTokenInterface::class,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $body->value(),
            ], HttpMethods::post())
            ->deserialize();
    }

    /**
     * @param mixed ...$scopes
     * @return string
     */
    protected static function buildOAuthString(...$scopes): string
    {
        return implode(' ', Arr::flatten($scopes));
    }

    /**
     * @return string|null
     */
    protected static function getTokenExchangeBaseUri()
    {
        if (empty(static::$tokenExchangeBaseUri)) {
            throw new LogicException(sprintf('You must instantiate "$tokenExchangeBaseUri" or override the "%s" method.', __METHOD__));
        }
        return static::$tokenExchangeBaseUri;
    }

    /**
     * @return array
     */
    final protected function getAuthenticationOption()
    {
        return [];
    }
}
