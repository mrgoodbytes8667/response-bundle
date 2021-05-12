<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\HttpClient\Common\HttpClient\ConfigurableScopingHttpClient;
use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Doctrine\Common\Annotations\Reader;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Psr\EventDispatcher\StoppableEventInterface;
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
    /**
     * @var ClientResponseInterface
     */
    protected $response;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * AbstractClient constructor.
     * @param HttpClientInterface $httpClient
     * @param string|null $userAgent
     * @param array $defaultOptionsByRegexp
     * @param string|null $defaultRegexp
     */
    public function __construct(protected HttpClientInterface $httpClient, ?string $userAgent, array $defaultOptionsByRegexp = [], string $defaultRegexp = null)
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
     * @param string $method = ['GET','HEAD','POST','PUT','DELETE','CONNECT','OPTIONS','TRACE','PATCH'][$any]
     * @param ClientResponseInterface|string|null $responseClass
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable|null $onDeserializeCallable If set, should be triggered by deserialize() on success, modifies/replaces results
     * @param callable|null $onSuccessCallable If set, should be triggered by deserialize() on success
     * @param array $params Extra params for makeFrom
     * @return ClientResponseInterface
     * @throws TransportExceptionInterface
     */
    public function request($url, \ReflectionMethod|string $caller = null, ?string $type = null, array $options = [], $method = 'GET', ClientResponseInterface|string|null $responseClass = null, array $context = [], ?callable $onDeserializeCallable = null, ?callable $onSuccessCallable = null, array $params = [])
    {
        $auth = null;
        if(!empty($this->reader)) {
            $classAuths = $this->reader->getClassAnnotations(new \ReflectionClass(static::class));
            $methodAnnotations = [];
            if (!is_null($caller)) {
                try {
                    if (is_string($caller)) {
                        $caller = new \ReflectionMethod($caller);
                    }
                    $methodAnnotations = $this->reader->getMethodAnnotations($caller);
                } catch (\ReflectionException) {

                }
            }
            $auth = Arr::where(array_merge($classAuths, $methodAnnotations), function ($value, $key) {
                return $value instanceof Auth;
            });
        }
        if (is_array($url)) {
            $url = implode('/', $url);
        }
        if (empty($url) || !is_string($url)) {
            throw new InvalidArgumentException();
        }
        $auth = $this->getAuthenticationOption();
        if (!empty($auth) && is_array($auth)) {
            $options = array_merge_recursive($options, $auth);
        }
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
        return $response->withResponse($this->httpClient->request($method, $this->buildURL($url), $options), $type, $context, $onDeserializeCallable, $onSuccessCallable);
    }

    /**
     * @return array
     */
    protected function getAuthenticationOption()
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

    /**
     * @param StoppableEventInterface $event
     * @param string|null $eventName
     * @return object
     */
    protected function dispatch(StoppableEventInterface $event, string $eventName = null)
    {
        if(empty($eventName)) {
            $eventName = get_class($event);
        }
        return $this->dispatcher->dispatch($event, $eventName);
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * @param Reader $reader
     * @return $this
     */
    public function setReader(Reader $reader): self
    {
        $this->reader = $reader;
        return $this;
    }
}