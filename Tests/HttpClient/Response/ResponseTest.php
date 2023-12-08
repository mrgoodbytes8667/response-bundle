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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

use function Symfony\Component\String\u;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class ResponseTest.
 */
class ResponseTest extends TestCase
{
    use TestFullSerializerTrait;
    use ClientExceptionResponseProviderTrait;

    /**
     * @dataProvider provideEmptySuccessfulResponse
     */
    public function testGetOnSuccessCallable($response, $headers)
    {
        // To cover getOnSuccessCallable() in DiscordResponse
        self::assertNull($response->getOnSuccessCallable());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     */
    public function testGetDeserializeContext($response, $headers)
    {
        // To cover getDeserializeContext() in DiscordResponse
        self::assertEmpty($response->getDeserializeContext());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     */
    public function testGetHeaders($response, $providedHeaders)
    {
        $count = count($providedHeaders);
        $providedHeaderKey = array_key_first($providedHeaders);
        $providedHeaderValue = array_shift($providedHeaders);

        $headers = $response->getHeaders();
        self::assertCount($count, $headers);
        self::assertArrayHasKey($providedHeaderKey, $headers);
        $header = array_shift($headers);
        $header = array_shift($header);
        self::assertEquals($providedHeaderValue, $header);
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     */
    public function testPassthroughMethods($response, $headers)
    {
        // To cover getStatusCode() in DiscordResponse
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        self::assertCount(1, $response->getHeaders());
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

        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, null);

        $this->expectException(TransportException::class);
        $discordResponse->getStatusCode();
    }

    /**
     * @dataProvider provide200Responses
     */
    public function testIsSuccess($code, $success)
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn($code);

        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, null);

        self::assertTrue($discordResponse->isSuccess());
    }

    /**
     * @dataProvider provide100Responses
     * @dataProvider provide300Responses
     * @dataProvider provide400Responses
     * @dataProvider provide500Responses
     */
    public function testIsNotSuccess($code, $success)
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willReturn($code);

        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, null);

        self::assertFalse($discordResponse->isSuccess());
    }

    public function testIsSuccessWithException()
    {
        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $response->method('getStatusCode')
            ->willThrowException(new TransportException());

        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, null);

        self::assertFalse($discordResponse->isSuccess());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     */
    public function testGetType($response, $headers)
    {
        // To cover getType() in DiscordResponse
        self::assertNull($response->getType());
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
                    $value,
                ],
            ]);

        yield ['response' => DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($ri, null), 'headers' => $headers];
    }

    /**
     * @depends testMake
     */
    public function testMakeFrom($clientResponse)
    {
        $discordResponse = DiscordResponse::makeFrom($clientResponse);

        self::assertInstanceOf(DiscordResponse::class, $discordResponse);
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
        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, Model::class, onSuccessCallable: function ($self, $results) {
            self::assertInstanceOf(DiscordResponse::class, $self);
            self::assertInstanceOf(Model::class, $results);
        });
        $discordResponse->deserialize();
        $discordResponse->onSuccessCallback()->onSuccessCallback(true);
    }

    public function testCallbackNoDeserialize()
    {
        $response = new MockStandaloneResponse(content: '{"bar":"bar","foo":"foo"}', headers: ['Content-Type' => 'application/json']);

        /** @var Model $discordResponse */
        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, Model::class, onSuccessCallable: function ($self, $results) {
            self::assertInstanceOf(DiscordResponse::class, $self);
            self::assertNull($results);
        })->onSuccessCallback()->onSuccessCallback()->onSuccessCallback(true);
    }

    /**
     * @depends testMake
     */
    public function testGetResponse(DiscordResponse $clientResponse)
    {
        self::assertInstanceOf(ResponseInterface::class, $clientResponse->getResponse());
    }

    /**
     * @depends testMake
     */
    public function testGetSerializer(DiscordResponse $clientResponse)
    {
        self::assertInstanceOf(SerializerInterface::class, $clientResponse->getSerializer());
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
        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, Model::class)->deserialize();
        self::assertInstanceOf(Model::class, $discordResponse);
        self::assertEquals('foo', $discordResponse->foo);
        self::assertEquals('bar', $discordResponse->getBar());
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

        $test = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The argument "$type" must be provided');

        self::assertTrue($test->deserialize());
    }

    /**
     * @dataProvider provideClientExceptionResponses
     *
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testDeserializationThrowsClientException($code)
    {
        $response = new MockStandaloneResponse(statusCode: $code);

        $test = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, Model::class);

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
        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, Model::class)->deserialize(false);

        self::assertInstanceOf(Model::class, $discordResponse);
        self::assertEquals('foo', $discordResponse->foo);
        self::assertEquals('bar', $discordResponse->getBar());
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
        $discordResponses = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, '\Bytes\ResponseBundle\Tests\Fixtures\Model[]')->deserialize(false);

        $discordResponse = array_shift($discordResponses);

        self::assertInstanceOf(Model::class, $discordResponse);
        self::assertEquals('foo', $discordResponse->foo);
        self::assertEquals('bar', $discordResponse->getBar());
    }

    /**
     * @depends testMake
     */
    public function testGetResults(DiscordResponse $clientResponse)
    {
        self::assertNull($clientResponse->getResults());
    }

    /**
     * @depends testMake
     */
    public function testGetExtraParams(DiscordResponse $clientResponse)
    {
        self::assertEmpty($clientResponse->getExtraParams());
    }

    /**
     * @dataProvider provideEmptySuccessfulResponse
     */
    public function testGetContent($response, $headers)
    {
        // To cover getContent() in DiscordResponse
        self::assertEmpty($response->getContent());
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

        $discordResponse = DiscordResponse::make($this->serializer, new EventDispatcher())->withResponse($response, null);

        self::assertInstanceOf(DiscordResponse::class, $discordResponse);

        return $discordResponse;
    }

    public function testMagicGetter()
    {
        $response = new DiscordResponse($this->serializer);

        self::assertNull($response->getNoResultsExpected());

        $response->setExtraParams(['abc' => 123]);

        self::assertEquals(123, $response->getAbc());

        self::assertNull($response->getNoResultsExpected());
    }
}
