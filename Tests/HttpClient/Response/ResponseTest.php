<?php

namespace Bytes\ResponseBundle\Tests\HttpClient\Response;

use Bytes\Common\Faker\Providers\MiscProvider;
use Bytes\ResponseBundle\HttpClient\Response\Response as DiscordResponse;
use Bytes\ResponseBundle\Tests\Fixtures\Model;
use Bytes\Tests\Common\ClientExceptionResponseProviderTrait;
use Bytes\Tests\Common\MockHttpClient\MockStandaloneResponse;
use Bytes\Tests\Common\TestFullSerializerTrait;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function Symfony\Component\String\u;

/**
 * Class ResponseTest
 * @package Bytes\ResponseBundle\Tests\HttpClient\Response
 */
class ResponseTest extends TestCase
{
    use TestFullSerializerTrait, ClientExceptionResponseProviderTrait;

    /**
     * @dataProvider provideEmptySuccessfulResponse
     * @param $response
     * @param $headers
     */
    public function testGetOnSuccessCallable($response, $headers)
    {
        // To cover getOnSuccessCallable() in DiscordResponse
        $this->assertNull($response->getOnSuccessCallable());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     * @param $response
     * @param $headers
     */
    public function testGetDeserializeContext($response, $headers)
    {
        // To cover getDeserializeContext() in DiscordResponse
        $this->assertEmpty($response->getDeserializeContext());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     * @param $response
     * @param $providedHeaders
     */
    public function testGetHeaders($response, $providedHeaders)
    {
        $count = count($providedHeaders);
        $providedHeaderKey = array_key_first($providedHeaders);
        $providedHeaderValue = array_shift($providedHeaders);

        $headers = $response->getHeaders();
        $this->assertCount($count, $headers);
        $this->assertArrayHasKey($providedHeaderKey, $headers);
        $header = array_shift($headers);
        $header = array_shift($header);
        $this->assertEquals($providedHeaderValue, $header);
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     * @param $response
     * @param $headers
     */
    public function testPassthroughMethods($response, $headers)
    {
        // To cover getStatusCode() in DiscordResponse
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertCount(1, $response->getHeaders());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetStatusCodeWithException()
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willThrowException(new TransportException());

        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, null);

        $this->expectException(TransportException::class);
        $discordResponse->getStatusCode();
    }


    /**
     * @dataProvider provide200Responses
     * @param $code
     * @param $success
     */
    public function testIsSuccess($code, $success)
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn($code);

        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, null);

        $this->assertTrue($discordResponse->isSuccess());
    }

    /**
     * @dataProvider provide100Responses
     * @dataProvider provide300Responses
     * @dataProvider provide400Responses
     * @dataProvider provide500Responses
     * @param $code
     * @param $success
     */
    public function testIsNotSuccess($code, $success)
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn($code);

        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, null);

        $this->assertFalse($discordResponse->isSuccess());
    }

    /**
     *
     */
    public function testIsSuccessWithException()
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willThrowException(new TransportException());

        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, null);

        $this->assertFalse($discordResponse->isSuccess());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     * @param $response
     * @param $headers
     */
    public function testGetType($response, $headers)
    {
        // To cover getType() in DiscordResponse
        $this->assertNull($response->getType());
    }

    /**
     * @return \Generator
     */
    public function provideEmptySuccessfulResponse()
    {
        /** @var Generator|MiscProvider $faker */
        $faker = Factory::create();
        $faker->addProvider(new MiscProvider($faker));

        $this->setUpSerializer();

        $header = u($faker->randomAlphanumericString(10, 'abcdefghijkmnopqrstuvwxyz'))->lower()->prepend('x-')->toString();

        $value = $faker->word();
        $headers[$header] = $value;

        $ri = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $ri->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);
        $ri->method('getHeaders')
            ->willReturn([
                $header => [
                    $value
                ]
            ]);

        yield ['response' => DiscordResponse::make($this->serializer)->withResponse($ri, null), 'headers' => $headers];
    }

    /**
     * @depends testMake
     * @param $clientResponse
     */
    public function testMakeFrom($clientResponse)
    {
        $discordResponse = DiscordResponse::makeFrom($clientResponse);

        $this->assertInstanceOf(DiscordResponse::class, $discordResponse);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCallback()
    {
        $response = new MockStandaloneResponse(content: '{"bar":"bar","foo":"foo"}', headers: ['Content-Type' => 'application/json']);

        /** @var DiscordResponse $discordResponse */
        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, Model::class, onSuccessCallable: function ($self, $results) {
            $this->assertInstanceOf(DiscordResponse::class, $self);
            $this->assertInstanceOf(Model::class, $results);
        });
        $discordResponse->deserialize();
        $discordResponse->callback()->callback(true);
    }

    /**
     *
     */
    public function testCallbackNoDeserialize()
    {
        $response = new MockStandaloneResponse(content: '{"bar":"bar","foo":"foo"}', headers: ['Content-Type' => 'application/json']);

        /** @var Model $discordResponse */
        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, Model::class, onSuccessCallable: function ($self, $results) {
            $this->assertInstanceOf(DiscordResponse::class, $self);
            $this->assertNull($results);
        })->callback()->callback()->callback(true);
    }

    /**
     * @depends testMake
     * @param DiscordResponse $clientResponse
     */
    public function testGetResponse(DiscordResponse $clientResponse)
    {
        $this->assertInstanceOf(ResponseInterface::class, $clientResponse->getResponse());
    }

    /**
     * @depends testMake
     * @param DiscordResponse $clientResponse
     */
    public function testGetSerializer(DiscordResponse $clientResponse)
    {
        $this->assertInstanceOf(SerializerInterface::class, $clientResponse->getSerializer());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testDeserialize()
    {
        $obj = new Model();
        $obj->foo = 'foo';
        $obj->setBar('bar');

        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);
        $response->method('getContent')
            ->willReturn('{"bar":"bar","foo":"foo"}');

        /** @var Model $discordResponse */
        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, Model::class)->deserialize();
        $this->assertInstanceOf(Model::class, $discordResponse);
        $this->assertEquals('foo', $discordResponse->foo);
        $this->assertEquals('bar', $discordResponse->getBar());
    }


    /**
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testDeserializationThrowsInvalidArgumentException()
    {
        $response = new MockStandaloneResponse(statusCode: Response::HTTP_NO_CONTENT);

        $test = DiscordResponse::make($this->serializer)->withResponse($response, null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The argument "$type" must be provided');

        $this->assertTrue($test->deserialize());
    }

    /**
     * @dataProvider provideClientExceptionResponses
     * @param $code
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testDeserializationThrowsClientException($code)
    {
        $response = new MockStandaloneResponse(statusCode: $code);

        $test = DiscordResponse::make($this->serializer)->withResponse($response, Model::class);

        $this->expectException(ClientExceptionInterface::class);

        $test->deserialize();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testDeserializeBypassValidationError()
    {
        $response = new MockStandaloneResponse(content: '{"bar":"bar","foo":"foo"}', statusCode: Response::HTTP_BAD_REQUEST, headers: ['Content-Type' => 'application/json']);

        /** @var Model $discordResponse */
        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, Model::class)->deserialize(false);

        $this->assertInstanceOf(Model::class, $discordResponse);
        $this->assertEquals('foo', $discordResponse->foo);
        $this->assertEquals('bar', $discordResponse->getBar());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testDeserializeArrayBypassValidationError()
    {
        $response = new MockStandaloneResponse(content: '{"bar":"bar","foo":"foo"}', statusCode: Response::HTTP_BAD_REQUEST, headers: ['Content-Type' => 'application/json']);

        /** @var Model[] $discordResponses */
        $discordResponses = DiscordResponse::make($this->serializer)->withResponse($response, '\Bytes\ResponseBundle\Tests\Fixtures\Model[]')->deserialize(false);

        $discordResponse = array_shift($discordResponses);

        $this->assertInstanceOf(Model::class, $discordResponse);
        $this->assertEquals('foo', $discordResponse->foo);
        $this->assertEquals('bar', $discordResponse->getBar());
    }

    /**
     * @depends testMake
     * @param DiscordResponse $clientResponse
     */
    public function testGetResults(DiscordResponse $clientResponse)
    {
        $this->assertNull($clientResponse->getResults());
    }

    /**
     * @depends testMake
     * @param DiscordResponse $clientResponse
     */
    public function testGetExtraParams(DiscordResponse $clientResponse)
    {
        $this->assertEmpty($clientResponse->getExtraParams());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     * @param $response
     * @param $headers
     */
    public function testGetContent($response, $headers)
    {
        // To cover getContent() in DiscordResponse
        $this->assertEmpty($response->getContent());
    }

    /**
     * @return DiscordResponse
     */
    public function testMake()
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willThrowException(new TransportException());

        $discordResponse = DiscordResponse::make($this->serializer)->withResponse($response, null);

        $this->assertInstanceOf(DiscordResponse::class, $discordResponse);

        return $discordResponse;
    }
}
