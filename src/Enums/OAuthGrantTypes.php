<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\EasyAdminChoiceEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use JetBrains\PhpStorm\Deprecated;

enum OAuthGrantTypes: string implements EasyAdminChoiceEnumInterface
{
    use StringBackedEnumTrait;

    case authorizationCode = 'authorization_code';
    case refreshToken = 'refresh_token';

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function authorizationCode(): OAuthGrantTypes
    {
        return OAuthGrantTypes::authorizationCode;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function refreshToken(): OAuthGrantTypes
    {
        return OAuthGrantTypes::refreshToken;
    }
}
