<?php

namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\BackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\BackedEnumTrait;

enum DayOfWeek: int implements BackedEnumInterface
{
    use BackedEnumTrait;

    case SUNDAY = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;

    /**
     * @return array<string, string>
     */
    public static function getAbbreviatedFormChoices(): array
    {
        return [
            'Su' => DayOfWeek::getFormChoiceValue(DayOfWeek::SUNDAY),
            'M' => DayOfWeek::getFormChoiceValue(DayOfWeek::MONDAY),
            'Tu' => DayOfWeek::getFormChoiceValue(DayOfWeek::TUESDAY),
            'W' => DayOfWeek::getFormChoiceValue(DayOfWeek::WEDNESDAY),
            'Th' => DayOfWeek::getFormChoiceValue(DayOfWeek::THURSDAY),
            'F' => DayOfWeek::getFormChoiceValue(DayOfWeek::FRIDAY),
            'Sa' => DayOfWeek::getFormChoiceValue(DayOfWeek::SATURDAY),
        ];
    }
}
