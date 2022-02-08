<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\BackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\BackedEnumTrait;
use ValueError;

enum ContentType: string implements BackedEnumInterface
{
    use BackedEnumTrait;

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
}
