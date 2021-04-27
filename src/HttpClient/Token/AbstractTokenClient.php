<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use BadMethodCallException;
use Bytes\ResponseBundle\Enums\HttpMethods;
use Bytes\ResponseBundle\Enums\OAuthGrantTypes;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\Objects\Push;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\UrlGenerator\UrlGeneratorTrait;
use Bytes\ResponseBundle\Validator\ValidatorTrait;
use Illuminate\Support\Arr;
use LogicException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractTokenClient
 * @package Bytes\ResponseBundle\HttpClient\Token
 *
 * @experimental
 */
abstract class AbstractTokenClient extends AbstractClient implements TokenExchangeInterface
{
    use SerializerAwareTrait, UrlGeneratorTrait, ValidatorTrait;

    /**
     * @var string|null
     */
    protected static $tokenExchangeBaseUri;

    /**
     * @var string
     */
    protected static $tokenExchangeEndpoint = 'oauth2/token';

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
    public function tokenExchange(string $code, ?string $route = null, string|callable|null $url = null, array $scopes = [], OAuthGrantTypes $grantType = null, ?callable $onSuccessCallable = null): ?AccessTokenInterface
    {
        $redirect = '';
        if (!empty($route)) {
            $redirect = $this->urlGenerator->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif (!empty($url)) {
            $redirect = is_callable($url) ? call_user_func($url, $code, $scopes) : $url;
        } else {
            throw new BadMethodCallException('Either $route or $url must be provided.');
        }
        $errors = $this->validator->validate($redirect, [
            new NotBlank(),
            new Url()
        ]);
        if (count($errors) > 0) {
            throw new ValidatorException((string)$errors);
        }

        $body = Push::createPush(value: empty($grantType) ? OAuthGrantTypes::authorizationCode()->value : $grantType->value, key: 'grant_type')
            ->push($redirect, 'redirect_uri')
            ->push(static::buildOAuthString($scopes), 'scope');

        $body = match ($grantType) {
            OAuthGrantTypes::authorizationCode(), null => $body->push($code, 'code'),
            OAuthGrantTypes::refreshToken() => $body->push($code, 'refresh_token'),
        };

        return $this->request($this->buildURL(static::getTokenExchangeBaseUri() . static::$tokenExchangeEndpoint),
            AccessTokenInterface::class,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $body->value(),
            ], HttpMethods::post(), onSuccessCallable: $onSuccessCallable, params: ['code' => $code])
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