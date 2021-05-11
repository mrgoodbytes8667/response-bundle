<?php


namespace Bytes\ResponseBundle\HttpClient\Api;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Event\ObtainValidTokenEvent;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\TokenSourceIdentifierTrait;
use Bytes\ResponseBundle\Security\SecurityTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AbstractApiClient
 * @package Bytes\ResponseBundle\HttpClient\Api
 *
 * @experimental
 */
abstract class AbstractApiClient extends AbstractClient
{
    use TokenSourceIdentifierTrait, SecurityTrait {
        SecurityTrait::getUser as getSecurityUser;
    }

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

    /**
     * @return AccessTokenInterface|null
     */
    protected function getToken(): ?AccessTokenInterface
    {
        /** @var ObtainValidTokenEvent $event */
        $event = $this->dispatch(ObtainValidTokenEvent::new(static::getIdentifier(), static::getTokenSource(), $this->getSecurityUser()));
        return $event->getToken();
    }
}