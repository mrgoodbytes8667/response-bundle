<?php

namespace Bytes\ResponseBundle\Tests\Token;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Token\AccessTokenCreateUpdateFromTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use DateInterval;
use PHPUnit\Framework\TestCase;

/**
 * Class AccessTokenCreateUpdateFromTraitTest.
 */
class AccessTokenCreateUpdateFromTraitTest extends TestCase
{
    use TestFakerTrait;

    public function testCreateFromAccessTokenAccessTokenOnly()
    {
        $accessToken = $this->faker->randomAlphanumericString();

        $token = $this->getMockForTrait(AccessTokenCreateUpdateFromTrait::class);

        $token = $token::createFromAccessToken($accessToken);

        self::assertEquals($accessToken, $token->getAccessToken());
    }

    public function testCreateFromAccessTokenFullToken()
    {
        $newToken = $this->getMockBuilder(AccessTokenInterface::class)->getMock();

        $token = $this->getMockForTrait(AccessTokenCreateUpdateFromTrait::class);
        $token->method('updateFromAccessToken')
            ->will(self::returnSelf());

        $token->setAccessToken($this->faker->randomAlphanumericString())
            ->setRefreshToken($this->faker->randomAlphanumericString())
            ->setExpiresIn(new DateInterval('PT'.$this->faker->numberBetween().'S'))
            ->setScope($this->faker->words(asText: true))
            ->setTokenType($this->faker->word());

        $token::createFromAccessToken($newToken);

        $this->addToAssertionCount(1);
    }
}
