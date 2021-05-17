<?php

namespace Bytes\ResponseBundle\Tests\HttpClient\Api;

use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;

/**
 * Class AbstractApiClientTest
 * @package Bytes\ResponseBundle\Tests\HttpClient\Api
 */
class AbstractApiClientTest extends TestCase
{
    /**
     *
     */
    public function testClient()
    {
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [HttpClient::create(), new EventDispatcher(), new GenericRetryStrategy(), '', '']);
        $this->assertInstanceOf(AbstractApiClient::class, $client);
    }
}