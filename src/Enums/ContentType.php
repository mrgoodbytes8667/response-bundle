<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use ValueError;

enum ContentType: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case imageGif = 'image/gif';
    case imageJpg = 'image/jpeg';
    case imagePng = 'image/png';
    case imageWebP = 'image/webp';
    case json = 'application/json';

    /**
     * @return string
     * @throws ValueError
     */
    public function getExtension(): string
    {
        return match ($this) {
            self::imageGif => 'gif',
            self::imageJpg => 'jpg',
            self::imagePng => 'png',
            self::imageWebP => 'webp',
            self::json => 'json',
            default => throw new ValueError('The value is invalid.'),
        };
    }

    public static function fromExtension($extension): ContentType
    {
        return match (strtolower($extension)) {
            'gif' => self::imageGif,
            'jpg', 'jpeg', 'jfif' => self::imageJpg,
            'png' => self::imagePng,
            'webp' => self::imageWebP,
            'json' => self::json,
            default => throw new ValueError('The value is invalid.'),
        };
    }
}
