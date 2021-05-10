<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use BadMethodCallException;
use Bytes\ResponseBundle\Enums\HttpMethods;
use Bytes\ResponseBundle\Enums\OAuthGrantTypes;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\Interfaces\ClientTokenResponseInterface;
use Bytes\ResponseBundle\Objects\Push;
use Bytes\ResponseBundle\Routing\OAuthInterface;
use Bytes\ResponseBundle\Routing\UrlGeneratorTrait;
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
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
     * @var string|null
     */
    protected static $tokenExchangeDeserializationClass;

    /**
     * @var string
     */
    protected static $tokenExchangeEndpoint = 'oauth2/token';

    /**
     * @var OAuthInterface|null
     */
    protected $oAuth = null;

    /**
     * AbstractTokenClient constructor.
     * @param HttpClientInterface $httpClient
     * @param string|null $userAgent
     * @param bool $revokeOnRefresh
     * @param bool $fireRevokeOnRefresh
     * @param array $defaultOptionsByRegexp
     * @param string|null $defaultRegexp
     */
    public function __construct(HttpClientInterface $httpClient, ?string $userAgent, protected bool $revokeOnRefresh, protected bool $fireRevokeOnRefresh, array $defaultOptionsByRegexp = [], string $defaultRegexp = null)
    {
        parent::__construct($httpClient, $userAgent, $defaultOptionsByRegexp, $defaultRegexp);
        $this->setupRevokeOnRefresh($revokeOnRefresh, $fireRevokeOnRefresh);
    }

    /**
     * Overloadable method to setup the revoke/fire on refresh variables
     * @param bool $revokeOnRefresh
     * @param bool $fireRevokeOnRefresh
     * @return $this
     */
    public function setupRevokeOnRefresh(bool $revokeOnRefresh, bool $fireRevokeOnRefresh): self {
        if($revokeOnRefresh) {
            $this->fireRevokeOnRefresh = false; // Will be fired by the revoke regardless
        }

        return $this;
    }

    /**
     * Exchanges the provided code (or token) for a (new) access token
     * @param string $code
     * @param string|null $route Either $route or $url (or setOAuth(()) is required, $route takes precedence over $url
     * @param string|null|callable(string, array) $url Either $route or $url (or setOAuth(()) is required, $route takes precedence over $url
     * @param array $scopes
     * @param OAuthGrantTypes|null $grantType
     * @param ClientTokenResponseInterface|string|null $responseClass
     * @param callable|null $onDeserializeCallable If set, should be triggered by deserialize() on success, modifies/replaces results
     * @param callable|null $onSuccessCallable If set, should be triggered by deserialize() on success
     * @return ClientTokenResponseInterface|null
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function tokenExchange(string $code, ?string $route = null, string|callable|null $url = null, array $scopes = [], OAuthGrantTypes $grantType = null, ClientTokenResponseInterface|string|null $responseClass = null, ?callable $onDeserializeCallable = null, ?callable $onSuccessCallable = null): ?ClientTokenResponseInterface
    {
        $redirect = '';
        if (!empty($route)) {
            $redirect = $this->urlGenerator->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif (!empty($url)) {
            $redirect = is_callable($url) ? call_user_func($url, $code, $scopes) : $url;
        } elseif(!is_null($this->oAuth)) {
            $redirect = $this->oAuth->getRedirect();
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

        if(empty($scopes) && !is_null($this->oAuth))
        {
            $scopes = $this->oAuth->getScopes();
        }

        $grantType = empty($grantType) ? OAuthGrantTypes::authorizationCode()->value : $grantType->value;

        $body = Push::createPush(value: $grantType, key: 'grant_type')
            ->push($redirect, 'redirect_uri')
            ->push(static::buildOAuthString($scopes), 'scope');

        switch ($grantType ?? OAuthGrantTypes::authorizationCode()) {
            case OAuthGrantTypes::authorizationCode():
                $body = $body->push($code, 'code');
                break;
            case OAuthGrantTypes::refreshToken():
                $body = $body->push($code, 'refresh_token');
                break;
        }

        $body = $this->normalizeTokenExchangeBody($body);

        return $this->request($this->buildURL(static::getTokenExchangeBaseUri() . static::$tokenExchangeEndpoint),
            type: static::getTokenExchangeDeserializationClass(),
            options: [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $body->value(),
            ], method: HttpMethods::post(), responseClass: $responseClass, onDeserializeCallable: $onDeserializeCallable,
            onSuccessCallable: $onSuccessCallable, params: ['code' => $code]);
    }

    /**
     * @param Push $body
     * @return Push
     */
    protected function normalizeTokenExchangeBody(Push $body): Push
    {
        return $body;
    }

    /**
     * @param mixed ...$scopes
     * @return string
     */
    public static function buildOAuthString(...$scopes): string
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
     * @return string|null
     */
    protected static function getTokenExchangeDeserializationClass()
    {
        if (empty(static::$tokenExchangeDeserializationClass)) {
            throw new LogicException(sprintf('You must instantiate "$tokenExchangeDeserializationClass" or override the "%s" method.', __METHOD__));
        }
        return static::$tokenExchangeDeserializationClass;
    }

    /**
     * @return OAuthInterface|null
     */
    public function getOAuth(): ?OAuthInterface
    {
        return $this->oAuth;
    }

    /**
     * @param OAuthInterface|null $oAuth
     * @return $this
     */
    public function setOAuth(?OAuthInterface $oAuth): self
    {
        $this->oAuth = $oAuth;
        return $this;
    }

    /**
     * @return array
     */
    final protected function getAuthenticationOption()
    {
        return [];
    }
}