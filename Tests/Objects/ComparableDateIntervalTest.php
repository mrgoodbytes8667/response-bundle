<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\ResponseBundle\Exception\LargeDateIntervalException;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Faker\Factory;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class ComparableDateIntervalTest
 * @package Bytes\ResponseBundle\Tests\Objects
 */
class ComparableDateIntervalTest extends TestCase
{
    /**
     *
     */
    public function testGetTotalSeconds()
    {
        $start = new DateTimeImmutable('2022-10-24T19:18:17+00:00');
        $end = new DateTimeImmutable('2022-10-27T14:20:58+00:00');
        $this->assertEquals(900, ($this->getTestClass())::getTotalSeconds(900));
        $this->assertEquals(900, ($this->getTestClass())::getTotalSeconds(new DateInterval('PT900S')));
        $this->assertEquals(900, ($this->getTestClass())::getTotalSeconds(DateInterval::createFromDateString('15 minutes')));
        $this->assertEquals(900, ($this->getTestClass())::getTotalSeconds(DateInterval::createFromDateString('900 seconds')));
        $this->assertEquals(90061, ($this->getTestClass())::getTotalSeconds(DateInterval::createFromDateString('1 day, 1 hour, 1 minute, 1 second')));
        $this->assertEquals(241361, ($this->getTestClass())::getTotalSeconds($start->diff($end)));
    }

    /**
     *
     */
    public function testNormalizeToSeconds()
    {
        $start = new DateTimeImmutable('2022-10-24T19:18:17+00:00');
        $end = new DateTimeImmutable('2022-10-27T14:20:58+00:00');
        $this->assertEquals(900, ($this->getTestClass())::normalizeToSeconds(900));
        $this->assertEquals(900, ($this->getTestClass())::normalizeToSeconds(new DateInterval('PT900S')));
        $this->assertEquals(900, ($this->getTestClass())::normalizeToSeconds('PT900S'));
        $this->assertEquals(900, ($this->getTestClass())::normalizeToSeconds(DateInterval::createFromDateString('15 minutes')));
        $this->assertEquals(900, ($this->getTestClass())::normalizeToSeconds(DateInterval::createFromDateString('900 seconds')));
        $this->assertEquals(90061, ($this->getTestClass())::normalizeToSeconds(DateInterval::createFromDateString('1 day, 1 hour, 1 minute, 1 second')));
        $this->assertEquals(241361, ($this->getTestClass())::normalizeToSeconds($start->diff($end)));
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     */
    public function testNormalizeToSecondsSpec($spec)
    {
        $this->assertEquals(900, ($this->getTestClass())::normalizeToSeconds($spec));
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     */
    public function testNormalizeToDateIntervalSpec($spec)
    {
        $testInterval = ($this->getTestClass())::normalizeToSeconds(new DateInterval('PT900S'));
        $this->assertEquals($testInterval, ($this->getTestClass())::normalizeToSeconds(($this->getTestClass())::normalizeToDateInterval($spec)));
    }

    /**
     * @return class-string<ComparableDateInterval>
     */
    public function getTestClass(): string
    {
        return ComparableDateInterval::class;
    }

    /**
     *
     */
    public function testSecondsToInterval()
    {
        $this->assertEquals(15, ($this->getTestClass())::secondsToInterval(900)->i);
    }

    /**
     *
     */
    public function testGetTotalSecondsNegative()
    {
        $start = new DateTimeImmutable('2022-10-24T19:18:17+00:00');
        $end = new DateTimeImmutable('2022-10-27T14:20:58+00:00');
        $this->assertEquals(-900, ($this->getTestClass())::getTotalSeconds(DateInterval::createFromDateString('15 minutes ago')));
        $this->assertEquals(-900, ($this->getTestClass())::getTotalSeconds(DateInterval::createFromDateString('900 seconds ago')));
        $interval = new DateInterval('PT900S');
        $interval->invert = 1;
        $this->assertEquals(-900, ($this->getTestClass())::getTotalSeconds($interval));
        $this->assertEquals(-90061, ($this->getTestClass())::getTotalSeconds(DateInterval::createFromDateString('1 day, 1 hour, 1 minute, 1 second ago')));
        $this->assertEquals(-241361, ($this->getTestClass())::getTotalSeconds($end->diff($start)));
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     * @throws Exception
     */
    public function testCompare($spec)
    {
        $interval = ($this->getTestClass())::create($spec);

        $this->assertEquals(ComparableDateInterval::INSTANCE_GREATER_THAN, $interval->compare(new DateInterval('PT30S')));
        $this->assertEquals(ComparableDateInterval::INSTANCE_GREATER_THAN, $interval->compare(DateInterval::createFromDateString('yesterday')));
        $this->assertEquals(ComparableDateInterval::INSTANCE_EQUALS, $interval->compare(new DateInterval('PT900S')));
        $this->assertEquals(ComparableDateInterval::INSTANCE_EQUALS, $interval->compare(new DateInterval('PT15M')));
        $this->assertEquals(ComparableDateInterval::INSTANCE_LESS_THAN, $interval->compare(new DateInterval('PT30M')));

        $testInterval = new DateInterval('PT900S');
        $testInterval->f = 500;
        $this->assertEquals(ComparableDateInterval::INSTANCE_LESS_THAN, $interval->compare($testInterval));
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     * @throws Exception
     */
    public function testEquals($spec)
    {
        $interval = ($this->getTestClass())::create($spec);

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
     * @return Generator
     * @throws Exception
     */
    public function provideIntervalCreateArgsNumberInterval()
    {
        yield [900];
        yield [new DateInterval('PT900S')];
        yield [ComparableDateInterval::create(900)];
    }

    /**
     * @return Generator
     * @throws Exception
     */
    public function provideIntervalCreateArgsString()
    {
        yield ['PT900S'];
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @param $spec
     * @throws Exception
     */
    public function testNormalize($spec)
    {
        $interval = ($this->getTestClass())::normalizeToDateInterval($spec);

        $this->assertInstanceOf(DateInterval::class, $interval);

        $testInterval = new DateInterval('PT900S');
        $this->assertEquals(($this->getTestClass())::getTotalSeconds($testInterval), ($this->getTestClass())::getTotalSeconds($interval));
    }

    /**
     *
     */
    public function testLargeIntervalsYears()
    {
        $this->expectException(LargeDateIntervalException::class);

        ComparableDateInterval::getTotalSeconds(new DateInterval("P5YT50S"));
    }

    /**
     *
     */
    public function testLargeIntervalsMonths()
    {
        $this->expectException(LargeDateIntervalException::class);

        ComparableDateInterval::getTotalSeconds(new DateInterval("P5MT50S"));
    }

    /**
     *
     */
    public function testLargeIntervalsViaDateDiff()
    {
        $start = new DateTimeImmutable('2010-10-24T19:18:17+00:00');
        $end = new DateTimeImmutable('2021-11-27T14:20:58+00:00');
        $this->assertEquals(350074961, ComparableDateInterval::getTotalSeconds($start->diff($end)));
    }

    /**
     *
     */
    public function testLargeIntervalHasIntervalSet()
    {
        $interval = new DateInterval("P5YT50S");
        try {
            ComparableDateInterval::getTotalSeconds($interval);
        } catch (LargeDateIntervalException $exception) {
            $this->assertEquals($interval, $exception->getInterval());
        }
    }

    /**
     *
     */
    public function testIntervalToMinutes()
    {
        $this->assertEquals(120, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT2H1S'), 'round'));
        $this->assertEquals(125, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT2H4M51S'), 'round'));
        $this->assertEquals(105, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT1H45M29S'), 'round'));
        $this->assertEquals(62, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT1H1M1S'), 'ceil'));
        $this->assertEquals(179, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT2H59M59S'), 'floor'));
        $this->assertEquals(120, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT2H1S'), manipulator: 'round'));
        $this->assertEquals(125, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT2H4M51S'), manipulator: 'round'));
        $this->assertEquals(105, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT1H45M29S'), manipulator: 'round'));
        $this->assertEquals(62, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT1H1M1S'), manipulator: 'ceil'));
        $this->assertEquals(179, ($this->getTestClass())::getTotalMinutes(new DateInterval('PT2H59M59S'), manipulator: 'floor'));
    }

    /**
     *
     */
    public function testIntervalToHoursMissingSecondArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalHours(new DateInterval('PT15M'));
    }

    /**
     *
     */
    public function testIntervalToMinutesMissingSecondArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalMinutes(new DateInterval('PT15M'));
    }

    /**
     *
     */
    public function testIntervalToHoursAdditionalThirdArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalHours(new DateInterval('PT15M'), 'ceil', 3);
    }

    /**
     *
     */
    public function testIntervalToMinutesAdditionalThirdArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalMinutes(new DateInterval('PT15M'), 'ceil', 3);
    }

    /**
     *
     */
    public function testIntervalToHoursInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalHours(new DateInterval('PT15M'), 'abc123');
    }

    /**
     *
     */
    public function testIntervalToMinutesInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalMinutes(new DateInterval('PT15M'), 'abc123');
    }

    /**
     *
     */
    public function testIntervalToHours()
    {
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT2H'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT2H5M'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT1H45M'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT1H1M'), 'ceil'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT2H59M'), 'floor'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT2H'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT2H5M'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT1H45M'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT1H1M'), manipulator: 'ceil'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalHours(new DateInterval('PT2H59M'), manipulator: 'floor'));
    }

    /**
     *
     */
    public function testIntervalToDaysInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalDays(new DateInterval('PT15M'), 'abc123');
    }

    /**
     *
     */
    public function testIntervalToDaysAdditionalThirdArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalDays(new DateInterval('PT15M'), 'ceil', 3);
    }

    /**
     *
     */
    public function testIntervalToDaysMissingSecondArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        ($this->getTestClass())::getTotalDays(new DateInterval('PT15M'));
    }

    /**
     *
     */
    public function testIntervalToDays()
    {
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2D'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2DT2H'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2DT2H5M'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P1DT12H45M'), 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P1DT1H1M'), 'ceil'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2DT23H59M'), 'floor'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2D'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2DT2H'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2DT2H5M'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P1DT12H45M'), manipulator: 'round'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P1DT1H1M'), manipulator: 'ceil'));
        $this->assertEquals(2, ($this->getTestClass())::getTotalDays(new DateInterval('P2DT23H59M'), manipulator: 'floor'));
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

        $seconds = ($this->getTestClass())::getTotalSeconds($interval);
        $this->assertLessThan(0, $seconds);
    }

    public function testInvalidConstructor()
    {
        $this->expectException(Exception::class);
        new ($this->getTestClass())('abc123');
    }
}
