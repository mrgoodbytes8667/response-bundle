<?php

namespace Bytes\ResponseBundle\Tests\HttpClient\Token;

use Bytes\Common\Faker\TestFakerTrait;
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
 * Class AbstractTokenClientTest
 * @package Bytes\ResponseBundle\Tests\HttpClient\Api
 */
class AbstractTokenClientTest extends TestCase
{
    use AssertClientResponseTrait, TestFullSerializerTrait, TestFakerTrait;

    /**
     *
     */
    public function testClient()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [HttpClient::create(), '', true, false]);
        $this->assertInstanceOf(AbstractTokenClient::class, $client);
    }

    /**
     * @dataProvider provideOAuthString
     * @param $scopes
     * @param $expected
     */
    public function testBuildOAuthString($scopes, $expected)
    {
        $this->assertEquals($expected, AbstractTokenClient::buildOAuthString($scopes));
    }

    /**
     * @return Generator
     */
    public function provideOAuthString()
    {
        yield ['scopes' => ['a', 'b', 'c'], 'expected' => 'a b c'];
        yield ['scopes' => ['a', ['b'], ['c']], 'expected' => 'a b c'];
    }

    /**
     *
     */
    public function testGetSetOAuth()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [HttpClient::create(), '', true, false]);
        $redirect = 'https://www.example.com';
        $oauth = $this->getMockForAbstractClass(AbstractOAuth::class, callOriginalConstructor: false);

        $this->assertNull($client->getOAuth());
        $client->setOAuth($oauth);
        $this->assertEquals($oauth, $client->getOAuth());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRequest()
    {
        $client = $this->getMockForAbstractClass(AbstractTokenClient::class, [MockClient::empty(), '', true, false]);
        $client->setResponse(Response::make($this->serializer, new EventDispatcher()));
        $response = $client->request($this->faker->url(), caller: __METHOD__);
        $this->assertResponseIsSuccessful($response);
        $this->assertResponseStatusCodeSame($response, Http::HTTP_NO_CONTENT);
        $this->assertResponseHasNoContent($response);
    }
}