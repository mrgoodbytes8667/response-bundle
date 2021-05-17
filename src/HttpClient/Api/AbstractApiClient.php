<?php


namespace Bytes\ResponseBundle\HttpClient\Api;


use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Event\ObtainValidTokenEvent;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
use Bytes\ResponseBundle\HttpClient\ApiRetryableHttpClient;
use Bytes\ResponseBundle\Security\SecurityTrait;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AbstractApiClient
 * @package Bytes\ResponseBundle\HttpClient\Api
 *
 * @experimental
 */
abstract class AbstractApiClient extends AbstractClient implements ApiClientInterface
{
    use SecurityTrait;

    /**
     * AbstractApiClient constructor.
     * @param HttpClientInterface $httpClient
     * @param RetryStrategyInterface|null $strategy
     * @param string $clientId
     * @param string|null $userAgent
     * @param array $defaultOptionsByRegexp
     * @param string|null $defaultRegexp
     * @param bool $retryAuth
     */
    public function __construct(HttpClientInterface $httpClient, ?RetryStrategyInterface $strategy, protected string $clientId, ?string $userAgent, array $defaultOptionsByRegexp = [], string $defaultRegexp = null, bool $retryAuth = true)
    {
        parent::__construct($httpClient, $userAgent, $defaultOptionsByRegexp, $defaultRegexp, $retryAuth);
        $this->httpClient = new ApiRetryableHttpClient($this->httpClient, $strategy, eventDispatcher: $this->getDispatcher(), apiClient: $this);
    }
}