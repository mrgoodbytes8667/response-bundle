<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\EasyAdminChoiceEnumInterface;
use Bytes\EnumSerializerBundle\Enums\FormChoiceEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use JetBrains\PhpStorm\Deprecated;
use ValueError;

enum ContentType: string implements EasyAdminChoiceEnumInterface, FormChoiceEnumInterface
{
    use StringBackedEnumTrait;

    case imageGif = 'image/gif';
    case imageJpg = 'image/jpeg';
    case imagePng = 'image/png';
    case imageWebP = 'image/webp';
    case json = 'application/json';

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function imageGif(): ContentType
    {
        return ContentType::imageGif;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function imageJpg(): ContentType
    {
        return ContentType::imageJpg;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function imagePng(): ContentType
    {
        return ContentType::imagePng;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function imageWebP(): ContentType
    {
        return ContentType::imageWebP;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function json(): ContentType
    {
        return ContentType::json;
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
            self::imageWebP => 'webp',
            self::json => 'json',
            default => throw new ValueError('The value is invalid.'),
        };
    }
}
