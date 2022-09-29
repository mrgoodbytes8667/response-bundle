<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\ResponseBundle\Exception\LargeDateIntervalException;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use Bytes\ResponseBundle\Objects\LargeComparableDateInterval;
use DateInterval;
use Exception;
use Faker\Factory;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LargeComparableDateIntervalTest extends ComparableDateIntervalTest
{

    /**
     *
     */
    public function testGetTotalSeconds()
    {
        $this->assertEquals(900, LargeComparableDateInterval::getTotalSeconds(900));
        $this->assertEquals(900, LargeComparableDateInterval::getTotalSeconds(new DateInterval('PT900S')));
    }

    /**
     *
     */
    public function testSecondsToInterval()
    {
        $this->assertEquals(15, LargeComparableDateInterval::secondsToInterval(900)->i);
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     * @throws Exception
     */
    public function testCompare($spec)
    {
        $interval = LargeComparableDateInterval::create($spec);

        $this->assertEquals(LargeComparableDateInterval::INSTANCE_GREATER_THAN, $interval->compare(new DateInterval('PT30S')));
        $this->assertEquals(LargeComparableDateInterval::INSTANCE_GREATER_THAN, $interval->compare(DateInterval::createFromDateString('yesterday')));
        $this->assertEquals(LargeComparableDateInterval::INSTANCE_EQUALS, $interval->compare(new DateInterval('PT900S')));
        $this->assertEquals(LargeComparableDateInterval::INSTANCE_EQUALS, $interval->compare(new DateInterval('PT15M')));
        $this->assertEquals(LargeComparableDateInterval::INSTANCE_LESS_THAN, $interval->compare(new DateInterval('PT30M')));

        $testInterval = new DateInterval('PT900S');
        $testInterval->f = 500;
        $this->assertEquals(LargeComparableDateInterval::INSTANCE_LESS_THAN, $interval->compare($testInterval));
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     * @throws Exception
     */
    public function testEquals($spec)
    {
        $interval = LargeComparableDateInterval::create($spec);

        $this->assertFalse($interval->equals(new DateInterval('PT30S')));
        $this->assertFalse($interval->equals(DateInterval::createFromDateString('yesterday')));
        $this->assertTrue($interval->equals(new DateInterval('PT900S')));
        $this->assertTrue($interval->equals(new DateInterval('PT15M')));
        $this->assertFalse($interval->equals(new DateInterval('PT30M')));

        $testInterval = new DateInterval('PT900S');
        $testInterval->f = 500;
        $this->assertFalse($interval->equals($testInterval));
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @param $spec
     * @throws Exception
     */
    public function testNormalize($spec)
    {
        $interval = LargeComparableDateInterval::normalizeToDateInterval($spec);

        $this->assertInstanceOf(DateInterval::class, $interval);

        $testInterval = new DateInterval('PT900S');
        $this->assertEquals(LargeComparableDateInterval::getTotalSeconds($testInterval), LargeComparableDateInterval::getTotalSeconds($interval));
    }

    /**
     *
     */
    public function testLargeIntervals()
    {
        $this->assertEquals(157680050, LargeComparableDateInterval::getTotalSeconds(new DateInterval("P5YT50S")));
        $this->assertEquals(166665650, LargeComparableDateInterval::getTotalSeconds(new DateInterval("P5Y3M2WT50S")));
    }

    /**
     *
     */
    public function testIntervalToMinutes()
    {
        $this->assertEquals(120, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT2H1S'), 'round'));
        $this->assertEquals(125, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT2H4M51S'), 'round'));
        $this->assertEquals(105, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT1H45M29S'), 'round'));
        $this->assertEquals(62, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT1H1M1S'), 'ceil'));
        $this->assertEquals(179, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT2H59M59S'), 'floor'));
        $this->assertEquals(120, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT2H1S'), manipulator: 'round'));
        $this->assertEquals(125, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT2H4M51S'), manipulator: 'round'));
        $this->assertEquals(105, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT1H45M29S'), manipulator: 'round'));
        $this->assertEquals(62, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT1H1M1S'), manipulator: 'ceil'));
        $this->assertEquals(179, LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT2H59M59S'), manipulator: 'floor'));
    }

    /**
     *
     */
    public function testIntervalToHoursMissingSecondArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalHours(new DateInterval('PT15M'));
    }

    /**
     *
     */
    public function testIntervalToMinutesMissingSecondArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT15M'));
    }

    /**
     *
     */
    public function testIntervalToHoursAdditionalThirdArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalHours(new DateInterval('PT15M'), 'ceil', 3);
    }

    /**
     *
     */
    public function testIntervalToMinutesAdditionalThirdArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT15M'), 'ceil', 3);
    }

    /**
     *
     */
    public function testIntervalToHoursInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalHours(new DateInterval('PT15M'), 'abc123');
    }

    /**
     *
     */
    public function testIntervalToMinutesInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalMinutes(new DateInterval('PT15M'), 'abc123');
    }

    /**
     *
     */
    public function testIntervalToHours()
    {
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT2H'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT2H5M'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT1H45M'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT1H1M'), 'ceil'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT2H59M'), 'floor'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT2H'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT2H5M'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT1H45M'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT1H1M'), manipulator: 'ceil'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalHours(new DateInterval('PT2H59M'), manipulator: 'floor'));
    }

    /**
     *
     */
    public function testInvertedInterval()
    {
        $faker = Factory::create();
        $d1 = $faker->dateTimeBetween('-6 days');
        $d2 = $faker->dateTimeBetween('tomorrow', '3 days');
        $interval = $d2->diff($d1);

        $seconds = LargeComparableDateInterval::getTotalSeconds($interval);
        $this->assertLessThan(0, $seconds);
    }

    public function testInvalidConstructor()
    {
        $this->expectException(Exception::class);
        new LargeComparableDateInterval('abc123');
    }

    /**
     *
     */
    public function testIntervalToDaysInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalDays(new DateInterval('PT15M'), 'abc123');
    }

    /**
     *
     */
    public function testIntervalToDaysAdditionalThirdArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalDays(new DateInterval('PT15M'), 'ceil', 3);
    }

    /**
     *
     */
    public function testIntervalToDaysMissingSecondArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        LargeComparableDateInterval::getTotalDays(new DateInterval('PT15M'));
    }

    /**
     *
     */
    public function testIntervalToDays()
    {
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2D'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2DT2H'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2DT2H5M'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P1DT12H45M'), 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P1DT1H1M'), 'ceil'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2DT23H59M'), 'floor'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2D'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2DT2H'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2DT2H5M'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P1DT12H45M'), manipulator: 'round'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P1DT1H1M'), manipulator: 'ceil'));
        $this->assertEquals(2, LargeComparableDateInterval::getTotalDays(new DateInterval('P2DT23H59M'), manipulator: 'floor'));
    }
}
