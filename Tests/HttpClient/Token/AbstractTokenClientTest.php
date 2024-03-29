<?php

namespace Bytes\ResponseBundle\Tests\HttpClient\Token;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Enums\HttpMethods;
use Bytes\ResponseBundle\HttpClient\Response\Response;
use Bytes\ResponseBundle\HttpClient\Token\AbstractTokenClient;
use Bytes\ResponseBundle\Routing\AbstractOAuth;
use Bytes\ResponseBundle\Test\AssertClientResponseTrait;
use Bytes\Tests\Common\MockHttpClient\MockClient;
use Bytes\Tests\Common\TestFullSerializerTrait;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response as Http;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractTokenClientTest.
 */
class AbstractTokenClientTest extends TestCase
{
    use AssertClientResponseTrait;
    use TestFullSerializerTrait;
    use TestFakerTrait;

    public function testClient()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [HttpClient::create(), new EventDispatcher(), '', true, false]);
        self::assertInstanceOf(AbstractTokenClient::class, $client);
    }

    /**
     * @dataProvider provideOAuthString
     */
    public function testBuildOAuthString($scopes, $expected)
    {
        self::assertEquals($expected, AbstractTokenClient::buildOAuthString($scopes));
    }

    /**
     * @return Generator
     */
    public function provideOAuthString()
    {
        yield ['scopes' => ['a', 'b', 'c'], 'expected' => 'a b c'];
        yield ['scopes' => ['a', ['b'], ['c']], 'expected' => 'a b c'];
    }

    public function testGetSetOAuth()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [HttpClient::create(), new EventDispatcher(), '', true, false]);
        $redirect = 'https://www.example.com';
        $oauth = $this->getMockForAbstractClass(AbstractOAuth::class, callOriginalConstructor: false);

        self::assertNull($client->getOAuth());
        $client->setOAuth($oauth);
        self::assertEquals($oauth, $client->getOAuth());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRequest()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [MockClient::empty(), new EventDispatcher(), '', true, false]);
        $client->setResponse(Response::make($this->serializer, new EventDispatcher()));

        $response = $client->request($this->faker->url(), caller: __METHOD__);
        self::assertResponseIsSuccessful($response);
        self::assertResponseStatusCodeSame($response, Http::HTTP_NO_CONTENT);
        self::assertResponseHasNoContent($response);
    }

    /**
     * @dataProvider provideMethods
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRequestMethods($method)
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [MockClient::empty(), new EventDispatcher(), '', true, false]);
        $client->setResponse(Response::make($this->serializer, new EventDispatcher()));

        $response = $client->request($this->faker->url(), caller: __METHOD__, method: $method);
        self::assertResponseIsSuccessful($response);
        self::assertResponseStatusCodeSame($response, Http::HTTP_NO_CONTENT);
        self::assertResponseHasNoContent($response);
    }

    public function provideMethods()
    {
        yield ['method' => 'GET'];
        yield ['method' => HttpMethods::get];
        yield ['method' => HttpMethods::get->value];
    }

    public function testFinalMethods()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [HttpClient::create(), new EventDispatcher(), '', true, false]);

        // mergeAuth
        $options = $this->faker->words(3);
        self::assertEquals($options, $client->mergeAuth(options: $options));

        // getAuthenticationOption
        self::assertCount(0, $client->getAuthenticationOption());
    }
}
