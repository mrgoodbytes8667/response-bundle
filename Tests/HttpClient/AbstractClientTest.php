<?php

namespace Bytes\ResponseBundle\Tests\HttpClient;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\HttpClient\AbstractApiClient;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\AbstractTokenClient;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * Class AbstractClientTest
 * @package Bytes\ResponseBundle\Tests\HttpClient
 */
class AbstractClientTest extends TestCase
{
    use TestFakerTrait;

    /**
     * @dataProvider provideAccessTokens
     * @param $token
     * @param $accessToken
     * @param $refreshToken
     */
    public function testNormalizeAccessToken($token, $accessToken, $refreshToken)
    {
        $this->assertEquals($accessToken, AbstractClient::normalizeAccessToken($token));
        $this->assertEquals($accessToken, AbstractApiClient::normalizeAccessToken($token));
        $this->assertEquals($accessToken, AbstractTokenClient::normalizeAccessToken($token));
    }

    /**
     *
     */
    public function testNormalizeAccessTokenNoNulls()
    {
        $this->expectException(UnexpectedValueException::class);

        AbstractClient::normalizeAccessToken($this->createToken(), false, $this->faker->sentence());
    }

    /**
     * @param string|null $accessToken
     * @param string|null $refreshToken
     * @return AccessTokenInterface|MockObject
     */
    private function createToken(?string $accessToken = null, ?string $refreshToken = null)
    {
        $token = $this
            ->getMockBuilder(AccessTokenInterface::class)
            ->getMock();

        $token->method('getAccessToken')
            ->willReturn($accessToken);
        $token->method('getRefreshToken')
            ->willReturn($refreshToken);

        return $token;
    }

    /**
     * @dataProvider provideRefreshTokens
     * @param $token
     * @param $accessToken
     * @param $refreshToken
     */
    public function testNormalizeRefreshToken($token, $accessToken, $refreshToken)
    {
        $this->assertEquals($refreshToken, AbstractClient::normalizeRefreshToken($token));
        $this->assertEquals($refreshToken, AbstractApiClient::normalizeRefreshToken($token));
        $this->assertEquals($refreshToken, AbstractTokenClient::normalizeRefreshToken($token));
    }

    /**
     *
     */
    public function testNormalizeRefreshTokenNoNulls()
    {
        $this->expectException(UnexpectedValueException::class);

        AbstractClient::normalizeRefreshToken($this->createToken(), false, $this->faker->sentence());
    }

    /**
     * @return Generator
     */
    public function provideAccessTokens()
    {
        $this->setupFaker();

        $accessToken = $this->faker->randomAlphanumericString();
        $refreshToken = $this->faker->randomAlphanumericString();
        yield ['token' => $this->createToken($accessToken, $refreshToken), 'accessToken' => $accessToken, 'refreshToken' => $refreshToken];
        yield ['token' => $accessToken, 'accessToken' => $accessToken, 'refreshToken' => $refreshToken];
        yield ['token' => null, 'accessToken' => null, 'refreshToken' => null];
    }

    /**
     * @return Generator
     */
    public function provideRefreshTokens()
    {
        $this->setupFaker();

        $accessToken = $this->faker->randomAlphanumericString();
        $refreshToken = $this->faker->randomAlphanumericString();
        yield ['token' => $this->createToken($accessToken, $refreshToken), 'accessToken' => $accessToken, 'refreshToken' => $refreshToken];
        yield ['token' => $refreshToken, 'accessToken' => $accessToken, 'refreshToken' => $refreshToken];
        yield ['token' => null, 'accessToken' => null, 'refreshToken' => null];
    }
}