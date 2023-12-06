<?php

namespace Bytes\ResponseBundle\Tests\HttpClient;

use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use Bytes\ResponseBundle\Tests\Fixtures\HttpClient\SampleAnnotationClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;

class SampleAnnotationClientTest extends TestCase
{
    public function testGetSample()
    {
        $ri = $this
            ->getMockBuilder(ClientResponseInterface::class)
            ->getMock();

        $client = new SampleAnnotationClient(httpClient: HttpClient::create(), dispatcher: new EventDispatcher(), userAgent: 'Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1');
        self::expectException(TransportException::class);
        self::assertInstanceOf(ClientResponseInterface::class, $client->getSample(responseClass: $ri));
    }
}
