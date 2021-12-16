<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\Enum;

/**
 * Class ContentType
 * @package Bytes\ResponseBundle\Enums
 *
 * @method static self imageGif()
 * @method static self imageJpg()
 * @method static self imagePng()
 * @method static self imageWebP()
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
            'imageGif' => 'image/gif',
            'imageJpg' => 'image/jpeg',
            'imagePng' => 'image/png',
            'imageWebP' => 'image/webp',
            'json' => 'application/json',
        ];
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return match ($this->value) {
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/json' => 'json',
            default => throw new \BadMethodCallException('The value is invalid.'),
        };
    }
}