<?php

namespace Bytes\ResponseBundle\Objects;

use DateInterval;

/**
 * Unlike \Bytes\ResponseBundle\Objects\ComparableDateInterval this method assumes a 30 day month and a 365 day year
 */
class LargeComparableDateInterval extends ComparableDateInterval
{
    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseYears(DateInterval $interval): int
    {
        if ($interval->y > 0) {
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
        if ($interval->m > 0) {
            return ($interval->m * 30 * 86400);
        }

        return 0;
    }
}
