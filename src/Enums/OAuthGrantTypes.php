<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;

enum OAuthGrantTypes: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case authorizationCode = 'authorization_code';
    case refreshToken = 'refresh_token';
}
