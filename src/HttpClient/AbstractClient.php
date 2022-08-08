<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\HttpClient\Common\HttpClient\ConfigurableScopingHttpClient;
use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Annotations\Client;
use Bytes\ResponseBundle\Enums\ContentType;
use Bytes\ResponseBundle\Enums\HttpMethods;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Event\DispatcherTrait;
use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use Bytes\ResponseBundle\Objects\Push;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Doctrine\Common\Annotations\Reader;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use UnexpectedValueException;

/**
 * Class AbstractClient
 * @package Bytes\ResponseBundle\HttpClient
 *
 * @experimental
 */
abstract class AbstractClient
{
    use ClientTrait, DispatcherTrait;

    /**
     * @var ClientResponseInterface
     */
    protected $response;

    /**
     * AbstractClient constructor.
     * @param HttpClientInterface $httpClient
     * @param EventDispatcherInterface $dispatcher
     * @param string|null $userAgent
     * @param array $defaultOptionsByRegexp
     * @param string|null $defaultRegexp
     * @param bool $retryAuth
     */
    public function __construct(protected HttpClientInterface $httpClient, protected EventDispatcherInterface $dispatcher, ?string $userAgent, array $defaultOptionsByRegexp = [], string $defaultRegexp = null, private bool $retryAuth = true, private $parseAuth = true)
    {
        // Add user agent if not already set
        if (!empty($userAgent)) {
            foreach ($defaultOptionsByRegexp as $index => $options) {
                if (!array_key_exists('headers', $defaultOptionsByRegexp[$index])) {
                    $defaultOptionsByRegexp[$index]['headers']['User-Agent'] = $userAgent;
                }
                if (!array_key_exists('User-Agent', $defaultOptionsByRegexp[$index]['headers'])) {
                    $defaultOptionsByRegexp[$index]['headers']['User-Agent'] = $userAgent;
                }
            }
        }
        $this->httpClient = new ConfigurableScopingHttpClient($httpClient, $defaultOptionsByRegexp, ['query', 'body'], $defaultRegexp);
    }

    /**
     * @param AccessTokenInterface|string|null $token
     * @param bool $allowNull
     * @param string $message
     * @return string|null
     */
    public static function normalizeAccessToken(AccessTokenInterface|string|null $token, bool $allowNull = true, string $message = 'The parameter cannot be null.')
    {
        return self::normalizeToken($token, $allowNull, $message, 'getAccessToken');
    }

    /**
     * @param AccessTokenInterface|string|null $token
     * @param bool $allowNull
     * @param string $message
     * @param string $function
     * @return string|null
     */
    private static function normalizeToken(AccessTokenInterface|string|null $token, bool $allowNull, string $message, string $function)
    {
        if (empty($token)) {
            return self::normalizeTokenForNulls(null, $allowNull, $message);
        }
        if ($token instanceof AccessTokenInterface) {
            return self::normalizeTokenForNulls($token->{$function}() ?? null, $allowNull, $message);
        }
        return self::normalizeTokenForNulls($token, $allowNull, $message);
    }

    /**
     * @param string|null $token
     * @param bool $allowNull
     * @param string $message
     * @return string|null
     */
    private static function normalizeTokenForNulls(?string $token, bool $allowNull, string $message)
    {
        if (!empty($token) || $allowNull) {
            return $token;
        }
        throw new UnexpectedValueException($message);
    }

    /**
     * @param AccessTokenInterface|string|null $token
     * @param bool $allowNull
     * @param string $message
     * @return string|null
     */
    public static function normalizeRefreshToken(AccessTokenInterface|string|null $token, bool $allowNull = true, string $message = 'The parameter cannot be null.')
    {
        return self::normalizeToken($token, $allowNull, $message, 'getRefreshToken');
    }

    /**
     * @param ClientResponseInterface $response
     * @return $this
     */
    public function setResponse(ClientResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @param string|string[] $url
     * @param \ReflectionMethod|string|null $caller
     * @param string|null $type
     * @param array $options = HttpClientInterface::OPTIONS_DEFAULTS
     * @param HttpMethods|string $method = ['GET','HEAD','POST','PUT','DELETE','CONNECT','OPTIONS','TRACE','PATCH'][$any]
     * @param ClientResponseInterface|string|null $responseClass
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable|null $onDeserializeCallable If set, should be triggered by deserialize() on success, modifies/replaces results
     * @param callable|null $onSuccessCallable If set, should be triggered by deserialize() on success
     * @param array $params Extra params for makeFrom
     * @return ClientResponseInterface
     * @throws TransportExceptionInterface
     * @throws NoTokenException
     */
    public function request($url, \ReflectionMethod|string $caller = null, ?string $type = null, array $options = [], $method = 'GET', ClientResponseInterface|string|null $responseClass = null, array $context = [], ?callable $onDeserializeCallable = null, ?callable $onSuccessCallable = null, array $params = [])
    {
        if(is_null($caller)) {
            trigger_deprecation('mrgoodbytes8667/response-bundle', '2.0.0', 'Calling request() without the caller argument is deprecated and will cease working in a future version.');
        }
        if($method instanceof HttpMethods) {
            $method = $method->value;
        }
        if($this->parseAuth && !empty($this->reader)) {
            if (!is_null($caller)) {
                try {
                    if (is_string($caller)) {
                        $caller = new \ReflectionMethod($caller);
                    }
                    /** @var Auth $auth */
                    $auth = $this->reader->getMethodAnnotation($caller, Auth::class);
                    $auth?->setIdentifier($this->getIdentifier());
                    $auth?->setTokenSource($this->getTokenSource());
                } catch (\ReflectionException) {

                }
            }
        }
        if (is_array($url)) {
            $url = implode('/', $url);
        }
        if (empty($url) || !is_string($url)) {
            throw new InvalidArgumentException();
        }
        $options = $this->mergeAuth($auth ?? null, $options);
        if (!is_null($responseClass)) {
            if (is_string($responseClass) && is_subclass_of($responseClass, ClientResponseInterface::class)) {
                $response = $responseClass::makeFrom($this->response, $params);
            } else {
                $response = $responseClass;
            }
        } else {
            $response = clone $this->response;
            $response->setExtraParams($params);
        }
        $return = $this->httpClient->request($method, $this->buildURL($url), $options);
        return $response->withResponse($return, $type, $context, $onDeserializeCallable, $onSuccessCallable);
    }

    /**
     * Helper variant of request() that sets the header content-type to application/json
     * @param string|string[] $url
     * @param \ReflectionMethod|string|null $caller
     * @param string|null $type
     * @param array $options = HttpClientInterface::OPTIONS_DEFAULTS
     * @param string $method = ['GET','HEAD','POST','PUT','DELETE','CONNECT','OPTIONS','TRACE','PATCH'][$any]
     * @param ClientResponseInterface|string|null $responseClass
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable|null $onDeserializeCallable If set, should be triggered by deserialize() on success, modifies/replaces results
     * @param callable|null $onSuccessCallable If set, should be triggered by deserialize() on success
     * @param array $params Extra params for makeFrom
     * @return ClientResponseInterface
     * @throws TransportExceptionInterface
     * @throws NoTokenException
     */
    public function jsonRequest($url, \ReflectionMethod|string $caller = null, ?string $type = null, array $options = [], $method = 'GET', ClientResponseInterface|string|null $responseClass = null, array $context = [], ?callable $onDeserializeCallable = null, ?callable $onSuccessCallable = null, array $params = [])
    {
        $options['headers']['Content-Type'] = ContentType::json()->value;
        return $this->request(url: $url, caller: $caller, type: $type, options: $options, method: $method,
            responseClass: $responseClass, context: $context, onDeserializeCallable: $onDeserializeCallable,
            onSuccessCallable: $onSuccessCallable, params: $params);
    }

    /**
     * @param Auth|null $auth
     * @param array $options
     * @param bool $refresh
     * @param array|null $authHeader
     * @return array
     * @throws NoTokenException
     */
    public function mergeAuth(?Auth $auth = null, array $options = [], bool $refresh = false, array $authHeader = null): array
    {
        $authHeader = $authHeader ?? $this->getAuthenticationOption(auth: $auth ?? new Auth(), refresh: $refresh);
        if (!empty($authHeader) && is_array($authHeader)) {
            if(isset($options['auth_bearer']))
            {
                unset($options['auth_bearer']);
            }
            $options = array_merge_recursive($options, $authHeader);
        }
        return $options;
    }

    /**
     * @return $this
     */
    protected function resetToken(): self
    {
        return $this;
    }

    /**
     * @param Auth|null $auth
     * @param bool $refresh
     * @return array
     *
     * @throws NoTokenException
     */
    public function getAuthenticationOption(?Auth $auth = null, bool $refresh = false): array
    {
        return [];
    }

    /**
     * Return the client name
     * @return string
     */
    abstract public static function getDefaultIndexName(): string;

    /**
     * @param string $path
     * @param string $prepend
     * @return string
     */
    protected function buildURL(string $path, string $prepend = '')
    {
        return ($prepend ?? '') . $path;
    }
}