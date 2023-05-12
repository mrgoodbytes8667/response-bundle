<?php

namespace Bytes\ResponseBundle\Tests\Token;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\EnumSerializerBundle\Faker\FakerEnumProvider;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use Bytes\ResponseBundle\Token\AccessTokenTrait;
use Bytes\StringMaskBundle\Twig\StringMaskRuntime;
use DateInterval;
use Exception;
use Generator;
use PHPUnit\Framework\TestCase;
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
     * Ensure the StrinkMaskRuntime class is loaded
     */
    protected function setUp(): void
    {
        StringMaskRuntime::getMaskedString('');
    }

    /**
     *
     */
    public function testGetSetAccessToken()
    {
        $accessToken = $this->faker->randomAlphanumericString();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        self::assertNull($token->getAccessToken());

        $token->setAccessToken(null);
        self::assertNull($token->getAccessToken());

        $token->setAccessToken($accessToken);
        self::assertEquals($accessToken, $token->getAccessToken());

        self::assertNotEquals($token, $token->getAccessToken(true));
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
        self::assertNull($token->getIdentifier());

        $class = $token::class;

        $token->setIdentifier(null);
        self::assertEquals($class, $token->getIdentifier());

        $token->setIdentifier($class);
        self::assertEquals($class, $token->getIdentifier());
    }

    /**
     *
     */
    public function testGetSetTokenType()
    {
        $tokenType = $this->faker->word();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        self::assertNull($token->getTokenType());

        $token->setTokenType(null);
        self::assertNull($token->getTokenType());

        $token->setTokenType($tokenType);
        self::assertEquals($tokenType, $token->getTokenType());
    }

    /**
     *
     */
    public function testGetSetTokenSource()
    {
        $this->faker->addProvider(new FakerEnumProvider($this->faker));
        $tokenSource = $this->faker->randomEnum(TokenSource::class);

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        self::assertNull($token->getTokenSource());

        $token->setTokenSource(null);
        self::assertNull($token->getTokenSource());

        $token->setTokenSource($tokenSource);
        self::assertEquals($tokenSource, $token->getTokenSource());
    }

    /**
     *
     */
    public function testGetSetRefreshToken()
    {
        $refreshToken = $this->faker->randomAlphanumericString();

        $token = $this->getMockForTrait(AccessTokenTrait::class);
        self::assertNull($token->getRefreshToken());

        $token->setRefreshToken(null);
        self::assertNull($token->getRefreshToken());

        $token->setRefreshToken($refreshToken);
        self::assertEquals($refreshToken, $token->getRefreshToken());

        self::assertNotEquals($token, $token->getRefreshToken(true));
    }

    /**
     * @dataProvider provideScopes
     * @param $scope
     * @param $expected
     */
    public function testGetSetScope($scope, $expected)
    {
        $token = $this->getMockForTrait(AccessTokenTrait::class);
        self::assertEmpty($token->getScope());

        $token->setScope('');
        self::assertEmpty($token->getScope());

        $token->setScope([]);
        self::assertEmpty($token->getScope());

        $token->setScope($scope);
        self::assertEquals($expected, $token->getScope());
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
        self::assertNull($token->getUser());

        $token->setUser(null);
        self::assertNull($token->getUser());

        $token->setUser($user);
        self::assertEquals($user, $token->getUser());
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
        self::assertNull($token->getExpiresIn());

        $token->setExpiresIn(null);
        self::assertNull($token->getExpiresIn());

        $token->setExpiresIn($expiresIn);
        self::assertEquals($expiresIn, $token->getExpiresIn());
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
        self::assertNull($token->getExpiresIn());

        $token->setExpiresIn(null);
        self::assertNull($token->getExpiresIn());

        $token->setExpiresIn($seconds);
        self::assertEquals(ComparableDateInterval::getTotalSeconds($interval), ComparableDateInterval::getTotalSeconds($token->getExpiresIn()));
        self::assertEquals($seconds, ComparableDateInterval::getTotalSeconds($token->getExpiresIn()));
        self::assertEquals(ComparableDateInterval::secondsToInterval($seconds), $token->getExpiresIn());
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
     * @dataProvider provideExpiresAt
     * @param $expiresAt
     */
    public function testGetSetExpiresAt($expiresAt)
    {
        $token = $this->getMockForTrait(AccessTokenTrait::class);
        self::assertNull($token->getExpiresAt());

        $token->setExpiresAt($expiresAt);
        self::assertEquals($expiresAt, $token->getExpiresAt());
    }

    /**
     * @return Generator
     */
    public function provideExpiresAt()
    {
        $this->setupFaker();
        yield [$this->faker->dateTimeThisMonth()];
        yield [\DateTimeImmutable::createFromInterface($this->faker->dateTimeThisMonth())];
    }
}