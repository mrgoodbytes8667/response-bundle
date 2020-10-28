<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\Enum;

/**
 * Class OAuthGrantTypes
 * @package Bytes\ResponseBundle\Enums
 *
 * @method static self authorizationCode()
 * @method static self refreshToken()
 */
class OAuthGrantTypes extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'authorizationCode' => 'authorization_code',
            'refreshToken' => 'refresh_token',
        ];
    }
}