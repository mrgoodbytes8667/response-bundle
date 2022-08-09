<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use JetBrains\PhpStorm\Deprecated;

enum TokenSource: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case id = 'id';
    case user = 'user';
    case app = 'app';

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::app')]
    public static function app() {
        return static::app;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::user')]
    public static function user() {
        return static::user;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::id')]
    public static function id() {
        return static::id;
    }

    /**
     * @return array = ['ID' => "id", 'User' => "user", 'App' => "app"]
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
