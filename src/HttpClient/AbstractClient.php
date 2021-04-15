<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\HttpClient\Common\HttpClient\ConfigurableScopingHttpClient;
use Bytes\ResponseBundle\HttpClient\Response\Response;
use InvalidArgumentException;
use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AbstractClient
 * @package Bytes\ResponseBundle\HttpClient
 *
 * @experimental 
 */
class AbstractClient
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * AbstractClient constructor.
     * @param HttpClientInterface $httpClient
     * @param RetryStrategyInterface|null $strategy
     * @param string $clientId
     * @param string|null $userAgent
     * @param array $defaultOptionsByRegexp
     * @param string|null $defaultRegexp
     */
    public function __construct(protected HttpClientInterface $httpClient, ?RetryStrategyInterface $strategy, protected string $clientId, ?string $userAgent, array $defaultOptionsByRegexp = [], string $defaultRegexp = null)
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
        $this->httpClient = new RetryableHttpClient(new ConfigurableScopingHttpClient($httpClient, $defaultOptionsByRegexp, ['query', 'body'], $defaultRegexp), $strategy);
    }

    /**
     * @param string|string[] $url
     * @param string|null $type
     * @param array $options = HttpClientInterface::OPTIONS_DEFAULTS
     * @param string $method = ['GET','HEAD','POST','PUT','DELETE','CONNECT','OPTIONS','TRACE','PATCH'][$any]
     * @param Response|string|null $responseClass
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function request($url, ?string $type = null, array $options = [], $method = 'GET', Response|string|null $responseClass = null)
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
            if (is_string($responseClass) && is_subclass_of($responseClass, Response::class)) {
                $response = $responseClass::makeFrom($this->response);
            } else {
                $response = $responseClass;
            }
        } else {
            $response = $this->response;
        }
        return $response->withResponse($this->httpClient->request($method, $this->buildURL($url), $options), $type);
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
     * @return string
     */
    protected function buildURL(string $path)
    {
        return $path;
    }
}