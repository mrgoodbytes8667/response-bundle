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
}
