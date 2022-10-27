<?php

namespace Bytes\ResponseBundle\Objects;

use DateInterval;

/**
 * Unlike \Bytes\ResponseBundle\Objects\ComparableDateInterval this method assumes a 30 day month and a 365 day year
 * unless the days variable is set [the DateInterval object was created by DateTimeImmutable::diff() or DateTime::diff()]
 */
class LargeComparableDateInterval extends ComparableDateInterval
{
    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseYears(DateInterval $interval): int
    {
        if (!static::hasDaysVariable($interval) && $interval->y > 0) {
            return ($interval->y * 365 * 86400);
        }

        return 0;
    }

    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseMonths(DateInterval $interval): int
    {
        if (!static::hasDaysVariable($interval) && $interval->m > 0) {
            return ($interval->m * 30 * 86400);
        }

        return 0;
    }
}
