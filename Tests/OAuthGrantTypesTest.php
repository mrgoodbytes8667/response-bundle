<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\ResponseBundle\Enums\OAuthGrantTypes;
use PHPUnit\Framework\TestCase;
use Spatie\Enum\Phpunit\EnumAssertions;

class OAuthGrantTypesTest extends TestCase
{
    public function testRefreshToken()
    {
        EnumAssertions::assertIsEnum(OAuthGrantTypes::refreshToken());
        EnumAssertions::assertEqualsEnum(OAuthGrantTypes::refreshToken(), 'refresh_token');
        EnumAssertions::assertSameEnumValue(OAuthGrantTypes::refreshToken(), 'refresh_token');
        EnumAssertions::assertSameEnumLabel(OAuthGrantTypes::refreshToken(), 'refreshToken');
    }

    public function testAuthorizationCode()
    {
        EnumAssertions::assertIsEnum(OAuthGrantTypes::authorizationCode());
        EnumAssertions::assertEqualsEnum(OAuthGrantTypes::authorizationCode(), 'authorization_code');
        EnumAssertions::assertSameEnumValue(OAuthGrantTypes::authorizationCode(), 'authorization_code');
        EnumAssertions::assertSameEnumLabel(OAuthGrantTypes::authorizationCode(), 'authorizationCode');
    }
}
