<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\BackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\BackedEnumTrait;

enum TokenSource: string implements BackedEnumInterface
{
    use BackedEnumTrait;

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
