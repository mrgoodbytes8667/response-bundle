<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\Enum;
use function Symfony\Component\String\u;

/**
 * Class TokenSource
 * @package Bytes\ResponseBundle\Enums
 *
 * @method static self id()
 * @method static self user()
 * @method static self app()
 */
class TokenSource extends Enum
{
    /**
     * @return array
     */
    public static function formChoices(): array
    {
        return [
            'ID' => 'id',
            'User' => 'user',
            'App' => 'app',
        ];
    }
}