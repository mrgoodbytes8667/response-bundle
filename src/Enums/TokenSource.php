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

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function id(): TokenSource
    {
        return TokenSource::id;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function user(): TokenSource
    {
        return TokenSource::user;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function app(): TokenSource
    {
        return TokenSource::app;
    }
}
