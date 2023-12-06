<?php

namespace Bytes\ResponseBundle\Tests\Fixtures\HttpClient;

use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Annotations\Client;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;

#[Client(identifier: 'sample')]
class SampleAttributeClient extends AbstractClient
{
    /**
     * {@inheritDoc}
     */
    public static function getDefaultIndexName(): string
    {
        return '';
    }

    #[Auth(scopes: ['sample'])]
    public function getSample(ClientResponseInterface|string $responseClass): ClientResponseInterface
    {
        return $this->request('https://example.invalid', caller: __METHOD__, responseClass: $responseClass);
    }
}
