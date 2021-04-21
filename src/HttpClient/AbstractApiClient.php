<?php


namespace Bytes\ResponseBundle\HttpClient;


use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AbstractApiClient
 * @package Bytes\ResponseBundle\HttpClient
 *
 * @experimental
 */
abstract class AbstractApiClient extends AbstractClient
{
    /**
     * AbstractApiClient constructor.
     * @param HttpClientInterface $httpClient
     * @param RetryStrategyInterface|null $strategy
     * @param string $clientId
     * @param string|null $userAgent
     * @param array $defaultOptionsByRegexp
     * @param string|null $defaultRegexp
     */
    public function __construct(HttpClientInterface $httpClient, ?RetryStrategyInterface $strategy, protected string $clientId, ?string $userAgent, array $defaultOptionsByRegexp = [], string $defaultRegexp = null)
    {
        parent::__construct($httpClient, $userAgent, $defaultOptionsByRegexp, $defaultRegexp);
        $this->httpClient = new RetryableHttpClient($this->httpClient, $strategy);
    }
}