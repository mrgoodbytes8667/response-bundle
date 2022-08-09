<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use Bytes\ResponseBundle\Enums\FileExtension as Ext;
use JetBrains\PhpStorm\Deprecated;
use ValueError;

/**
 * @since 1.1.0
 * @version 5.0.0
 */
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

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::imageGif')]
    public static function imageGif()
    {
        return static::imageGif;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::imageJpg')]
    public static function imageJpg()
    {
        return static::imageJpg;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::imagePng')]
    public static function imagePng()
    {
        return static::imagePng;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::imageSvg')]
    public static function imageSvg()
    {
        return static::imageSvg;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::imageWebP')]
    public static function imageWebP()
    {
        return static::imageWebP;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::json')]
    public static function json()
    {
        return static::json;
    }

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
}
