<?php

namespace Bytes\ResponseBundle\Handler;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use Bytes\ResponseBundle\HttpClient\Token\TokenClientInterface;

use function Symfony\Component\String\u;

/**
 * Class HttpClientLocator.
 */
class HttpClientLocator extends Locator
{
    /**
     * @return AbstractClient|AbstractApiClient|TokenClientInterface|null
     */
    public function getClient(string $identifier, TokenSource $tokenSource, string $token = '-')
    {
        $tag = u($identifier)->append($token)->append($tokenSource->value)->upper()->toString();
        if ($this->locator->has($tag)) {
            return $this->locator->get($tag);
        }

        return null;
    }

    /**
     * @return AbstractApiClient|null
     */
    public function getApiClient(string $identifier, TokenSource $tokenSource)
    {
        return $this->getClient($identifier, $tokenSource, '-API-');
    }

    /**
     * @return TokenClientInterface|null
     */
    public function getTokenClient(string $identifier, TokenSource $tokenSource)
    {
        return $this->getClient($identifier, $tokenSource, '-TOKEN-');
    }
}
