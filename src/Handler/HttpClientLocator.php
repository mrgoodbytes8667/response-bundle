<?php


namespace Bytes\ResponseBundle\Handler;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use Bytes\ResponseBundle\HttpClient\Token\AbstractTokenClient;
use function Symfony\Component\String\u;

/**
 * Class HttpClientLocator
 * @package Bytes\ResponseBundle\Handler
 */
class HttpClientLocator extends Locator
{
    /**
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @param string $token
     * @return AbstractClient|AbstractApiClient|AbstractTokenClient|null
     */
    public function getClient(string $identifier, TokenSource $tokenSource, string $token = '-')
    {
        $tag = u($identifier)->append($token)->append($tokenSource->value)->upper()->toString();
        if($this->locator->has($tag))
        {
            return $this->locator->get($tag);
        }
        return null;
    }

    /**
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @return AbstractApiClient|null
     */
    public function getApiClient(string $identifier, TokenSource $tokenSource)
    {
        return $this->getClient($identifier, $tokenSource, '-API-');
    }

    /**
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @return AbstractTokenClient|null
     */
    public function getTokenClient(string $identifier, TokenSource $tokenSource)
    {
        return $this->getClient($identifier, $tokenSource, '-TOKEN-');
    }
}