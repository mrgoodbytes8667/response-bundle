<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;

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
}
