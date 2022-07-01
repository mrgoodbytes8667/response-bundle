<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use Bytes\ResponseBundle\Enums\FileExtension as Ext;
use ValueError;

enum ContentType: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case imageGif = 'image/gif';
    case imageJpg = 'image/jpeg';
    case imagePng = 'image/png';
    case imageSvg = 'image/svg+xml';
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
            self::imageSvg => 'svg',
            self::imageWebP => 'webp',
            self::json => 'json',
            default => throw new ValueError('The value is invalid.'),
        };
    }

    /**
     * @param $extension
     * @return ContentType
     */
    public static function fromExtension($extension): ContentType
    {
        return match (Ext::tryFrom(strtolower($extension))) {
            Ext::GIF => self::imageGif,
            Ext::JPG, Ext::JPEG, Ext::JPE, Ext::JIF, Ext::JFIF => self::imageJpg,
            Ext::APNG, Ext::PNG => self::imagePng,
            Ext::SVG => self::imageSvg,
            Ext::WEBP => self::imageWebP,
            Ext::JSON => self::json,
            default => throw new ValueError('The value is invalid.'),
        };
    }
}
