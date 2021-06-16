<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\Enum;

/**
 * Class ContentType
 * @package Bytes\ResponseBundle\Enums
 *
 * @method static self imageJpg()
 * @method static self imagePng()
 *
 * @method static self json()
 */
class ContentType extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'imageJpg' => 'image/jpeg',
            'imagePng' => 'image/png',
            'json' => 'application/json',
        ];
    }
}