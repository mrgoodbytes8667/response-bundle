<?php

namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;

/**
 * @since 5.0.0
 * @version 5.0.0
 */
enum FileExtension: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case GIF = 'gif';
    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case JPE = 'jpe';
    case JIF = 'jif';
    case JFIF = 'jfif';
    case APNG = 'apng';
    case PNG = 'png';
    case SVG = 'svg';
    case WEBP = 'webp';
    case JSON = 'json';
}
