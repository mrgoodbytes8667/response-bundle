<?php

namespace Bytes\ResponseBundle\Tests\HttpClient;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use Bytes\ResponseBundle\HttpClient\Token\AbstractTokenClient;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * Class AbstractClientTest.
 */
class AbstractClientTest extends TestCase
{
    use TestFakerTrait;

    /**
     * @dataProvider provideAccessTokens
     */
    public function testNormalizeAccessToken($token, $accessToken, $refreshToken)
    {
        self::assertEquals($accessToken, AbstractClient::normalizeAccessToken($token));
        self::assertEquals($accessToken, AbstractApiClient::normalizeAccessToken($token));
        self::assertEquals($accessToken, \Bytes\ResponseBundle\HttpClient\Token\AbstractTokenClient::normalizeAccessToken($token));
    }

    public function testNormalizeAccessTokenNoNulls()
    {
        $this->expectException(UnexpectedValueException::class);

        AbstractClient::normalizeAccessToken($this->createToken(), false, $this->faker->sentence());
    }

    /**
     * @return AccessTokenInterface|MockObject
     */
    private function createToken(string $accessToken = null, string $refreshToken = null)
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
     */
    public function testNormalizeRefreshToken($token, $accessToken, $refreshToken)
    {
        self::assertEquals($refreshToken, AbstractClient::normalizeRefreshToken($token));
        self::assertEquals($refreshToken, AbstractApiClient::normalizeRefreshToken($token));
        self::assertEquals($refreshToken, AbstractTokenClient::normalizeRefreshToken($token));
    }

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
