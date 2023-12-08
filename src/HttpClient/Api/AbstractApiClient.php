<?php

namespace Bytes\ResponseBundle\HttpClient\Api;

use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
use Bytes\ResponseBundle\HttpClient\ApiRetryableHttpClient;
use Bytes\ResponseBundle\Security\SecurityTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AbstractApiClient.
 *
 * @experimental
 */
abstract class AbstractApiClient extends AbstractClient implements ApiClientInterface
{
    use SecurityTrait;

    /**
     * AbstractApiClient constructor.
     */
    public function __construct(HttpClientInterface $httpClient, EventDispatcherInterface $dispatcher, ?RetryStrategyInterface $strategy, protected string $clientId, ?string $userAgent, array $defaultOptionsByRegexp = [], string $defaultRegexp = null, bool $retryAuth = true)
    {
        parent::__construct($httpClient, $dispatcher, $userAgent, $defaultOptionsByRegexp, $defaultRegexp, $retryAuth);
        $this->httpClient = new ApiRetryableHttpClient($this->httpClient, $strategy, eventDispatcher: $this->dispatcher, apiClient: $this);
    }
}
