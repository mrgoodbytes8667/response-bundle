<?php

namespace Bytes\ResponseBundle\Tests\Token;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use Bytes\ResponseBundle\Token\AccessTokenTrait;
use DateInterval;
use Exception;
use Generator;
use PHPUnit\Framework\TestCase;
use Spatie\Enum\Faker\FakerEnumProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use TypeError;

/**
 * Class AccessTokenTraitTest
 * @package Bytes\ResponseBundle\Tests\Token
 */
class AccessTokenTraitTest extends TestCase
{
    use TestFakerTrait;

    /**
     *
     */
    public function testGetSetAccessToken()
    {
        $accessToken = $this->faker->randomAlphanumericString();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getAccessToken());

        $token->setAccessToken(null);
        $this->assertNull($token->getAccessToken());

        $token->setAccessToken($accessToken);
        $this->assertEquals($accessToken, $token->getAccessToken());
    }

    /**
     *
     */
    public function testGetSetId()
    {
        $this->expectException(TypeError::class);
        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $token->getId();
    }

    /**
     *
     */
    public function testGetSetIdentifier()
    {
        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getIdentifier());

        $class = $token::class;

        $token->setIdentifier(null);
        $this->assertEquals($class, $token->getIdentifier());

        $token->setIdentifier($class);
        $this->assertEquals($class, $token->getIdentifier());
    }

    /**
     *
     */
    public function testGetSetTokenType()
    {
        $tokenType = $this->faker->word();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getTokenType());

        $token->setTokenType(null);
        $this->assertNull($token->getTokenType());

        $token->setTokenType($tokenType);
        $this->assertEquals($tokenType, $token->getTokenType());
    }

    /**
     *
     */
    public function testGetSetTokenSource()
    {
        $this->faker->addProvider(new FakerEnumProvider($this->faker));
        $tokenSource = $this->faker->randomEnum(TokenSource::class);

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getTokenSource());

        $token->setTokenSource(null);
        $this->assertNull($token->getTokenSource());

        $token->setTokenSource($tokenSource);
        $this->assertEquals($tokenSource, $token->getTokenSource());
    }

    /**
     *
     */
    public function testGetSetRefreshToken()
    {
        $refreshToken = $this->faker->randomAlphanumericString();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getRefreshToken());

        $token->setRefreshToken(null);
        $this->assertNull($token->getRefreshToken());

        $token->setRefreshToken($refreshToken);
        $this->assertEquals($refreshToken, $token->getRefreshToken());
    }

    /**
     * @dataProvider provideScopes
     * @param $scope
     * @param $expected
     */
    public function testGetSetScope($scope, $expected)
    {
        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertEmpty($token->getScope());

        $token->setScope('');
        $this->assertEmpty($token->getScope());

        $token->setScope([]);
        $this->assertEmpty($token->getScope());

        $token->setScope($scope);
        $this->assertEquals($expected, $token->getScope());
    }

    /**
     * @return Generator
     */
    public function provideScopes()
    {
        yield ['scopes' => ["channel:read:hype_train", "user:read:email"], 'expected' => "channel:read:hype_train user:read:email"];
        yield ['scopes' => "channel:read:hype_train user:read:email", 'expected' => "channel:read:hype_train user:read:email"];
    }

    /**
     *
     */
    public function testGetSetUser()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getUser());

        $token->setUser(null);
        $this->assertNull($token->getUser());

        $token->setUser($user);
        $this->assertEquals($user, $token->getUser());
    }

    /**
     * @dataProvider provideExpiresIn
     * @param DateInterval $interval
     * @param int $seconds
     * @throws Exception
     */
    public function testGetSetExpiresInInterval($interval, $seconds)
    {
        $expiresIn = $interval;

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getExpiresIn());

        $token->setExpiresIn(null);
        $this->assertNull($token->getExpiresIn());

        $token->setExpiresIn($expiresIn);
        $this->assertEquals($expiresIn, $token->getExpiresIn());
    }

    /**
     * @dataProvider provideExpiresIn
     * @param DateInterval $interval
     * @param int $seconds
     * @throws Exception
     */
    public function testGetSetExpiresInSeconds($interval, $seconds)
    {

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getExpiresIn());

        $token->setExpiresIn(null);
        $this->assertNull($token->getExpiresIn());

        $token->setExpiresIn($seconds);
        $this->assertEquals(ComparableDateInterval::getTotalSeconds($interval), ComparableDateInterval::getTotalSeconds($token->getExpiresIn()));
        $this->assertEquals($seconds, ComparableDateInterval::getTotalSeconds($token->getExpiresIn()));
        $this->assertEquals(ComparableDateInterval::secondsToInterval($seconds), $token->getExpiresIn());
    }

    /**
     * @return Generator
     */
    public function provideExpiresIn()
    {
        yield ['interval' => new DateInterval('PT259200S'), 'seconds' => 24 * 60 * 60 * 3];
        yield ['interval' => new DateInterval('PT300S'), 'seconds' => 300];
    }

    /**
     *
     */
    public function testGetSetExpiresAt()
    {
        $expiresAt = $this->faker->dateTimeThisMonth();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        $this->assertNull($token->getExpiresAt());

        $token->setExpiresAt($expiresAt);
        $this->assertEquals($expiresAt, $token->getExpiresAt());
    }
}