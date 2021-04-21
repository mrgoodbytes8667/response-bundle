<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\HttpClient\Common\HttpClient\ConfigurableScopingHttpClient;
use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
     * @param string|null $type
     * @param array $options = HttpClientInterface::OPTIONS_DEFAULTS
     * @param string $method = ['GET','HEAD','POST','PUT','DELETE','CONNECT','OPTIONS','TRACE','PATCH'][$any]
     * @param ClientResponseInterface|string|null $responseClass
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable(static, mixed)|null $onSuccessCallable If set, should be triggered by deserialize()/callback() on success
     * @param array $params Extra params for makeFrom
     * @return ClientResponseInterface
     * @throws TransportExceptionInterface
     */
    public function request($url, ?string $type = null, array $options = [], $method = 'GET', ClientResponseInterface|string|null $responseClass = null, array $context = [], ?callable $onSuccessCallable = null, array $params = [])
    {
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
        return $response->withResponse($this->httpClient->request($method, $this->buildURL($url), $options), $type, $context, $onSuccessCallable);
    }

    /**
     * @return array
     */
    protected function getAuthenticationOption()
    {
        return [];
    }

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