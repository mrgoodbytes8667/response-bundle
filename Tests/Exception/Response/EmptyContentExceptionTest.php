<?php

namespace Bytes\ResponseBundle\Tests\Exception\Response;

use Bytes\ResponseBundle\Exception\Response\EmptyContentException;
use Bytes\ResponseBundle\HttpClient\Response\Response;
use Bytes\Tests\Common\TestFullSerializerTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response as Http;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class EmptyContentExceptionTest.
 */
class EmptyContentExceptionTest extends TestCase
{
    use TestFullSerializerTrait;

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testGetResponse()
    {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockResponse->method('getContent')
            ->willReturn('');
        $mockResponse->method('getStatusCode')
            ->willReturn(Http::HTTP_NO_CONTENT);

        $response = Response::make($this->serializer, new EventDispatcher());
        $response = $response->withResponse($mockResponse, stdClass::class);
        $response->setThrowOnDeserializationWhenContentEmpty(true);

        try {
            $response->deserialize();
        } catch (EmptyContentException $exception) {
            self::assertInstanceOf(ResponseInterface::class, $exception->getResponse());
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testConstruct()
    {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockResponse->method('getContent')
            ->willReturn('');
        $mockResponse->method('getStatusCode')
            ->willReturn(Http::HTTP_NO_CONTENT);

        $response = Response::make($this->serializer, new EventDispatcher());
        $response = $response->withResponse($mockResponse, stdClass::class);
        $response->setThrowOnDeserializationWhenContentEmpty(true);

        $this->expectException(EmptyContentException::class);
        $response->deserialize();
    }
}
