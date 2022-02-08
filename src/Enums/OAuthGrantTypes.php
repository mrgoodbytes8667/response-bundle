<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\BackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\BackedEnumTrait;

enum OAuthGrantTypes: string implements BackedEnumInterface
{
    use BackedEnumTrait;

    case authorizationCode = 'authorization_code';
    case refreshToken = 'refresh_token';
}
