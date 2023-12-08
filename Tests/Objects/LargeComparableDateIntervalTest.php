<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\ResponseBundle\Objects\LargeComparableDateInterval;
use DateInterval;
use DateTimeImmutable;

class LargeComparableDateIntervalTest extends ComparableDateIntervalTest
{
    public function getTestClass(): string
    {
        return LargeComparableDateInterval::class;
    }

    public function testLargeIntervalsYears()
    {
        self::assertEquals(157680050, LargeComparableDateInterval::getTotalSeconds(new DateInterval('P5YT50S')));
        self::assertEquals(166665650, LargeComparableDateInterval::getTotalSeconds(new DateInterval('P5Y3M2WT50S')));
    }

    public function testLargeIntervalsMonths()
    {
        self::assertEquals(12960050, LargeComparableDateInterval::getTotalSeconds(new DateInterval('P5MT50S')));
    }

    public function testLargeIntervalsViaDateDiff()
    {
        $start = new DateTimeImmutable('2010-10-24T19:18:17+00:00');
        $end = new DateTimeImmutable('2021-11-27T14:20:58+00:00');

        self::assertEquals(350074961, LargeComparableDateInterval::getTotalSeconds($start->diff($end)));

        $interval = new DateInterval('P11Y1M2DT19H2M41S');
        self::assertEquals(349729361, LargeComparableDateInterval::getTotalSeconds($interval));
    }

    public function testLargeIntervalHasIntervalSet()
    {
        $this->expectNotToPerformAssertions();
    }
}
