<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\ResponseBundle\Exception\LargeDateIntervalException;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use DateInterval;
use Exception;
use Faker\Factory;
use Generator;
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
        $this->assertEquals(900, ComparableDateInterval::getTotalSeconds(900));
        $this->assertEquals(900, ComparableDateInterval::getTotalSeconds(new DateInterval('PT900S')));
    }

    /**
     *
     */
    public function testSecondsToInterval()
    {
        $this->assertEquals(15, ComparableDateInterval::secondsToInterval(900)->i);
    }

    /**
     * @dataProvider provideIntervalCreateArgsNumberInterval
     * @dataProvider provideIntervalCreateArgsString
     * @param $spec
     * @throws Exception
     */
    public function testCompare($spec)
    {
        $interval = ComparableDateInterval::create($spec);

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
        $interval = ComparableDateInterval::create($spec);

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
        $interval = ComparableDateInterval::normalizeToDateInterval($spec);

        $this->assertInstanceOf(DateInterval::class, $interval);

        $testInterval = new DateInterval('PT900S');
        $this->assertEquals(ComparableDateInterval::getTotalSeconds($testInterval), ComparableDateInterval::getTotalSeconds($interval));
    }

    /**
     *
     */
    public function testLargeIntervals()
    {
        $this->expectException(LargeDateIntervalException::class);

        ComparableDateInterval::getTotalSeconds(new DateInterval("P5YT50S"));
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
        $this->assertEquals(120, ComparableDateInterval::getTotalMinutes(new DateInterval('PT2H1S'), 'round'));
        $this->assertEquals(125, ComparableDateInterval::getTotalMinutes(new DateInterval('PT2H4M51S'), 'round'));
        $this->assertEquals(105, ComparableDateInterval::getTotalMinutes(new DateInterval('PT1H45M29S'), 'round'));
        $this->assertEquals(62, ComparableDateInterval::getTotalMinutes(new DateInterval('PT1H1M1S'), 'ceil'));
        $this->assertEquals(179, ComparableDateInterval::getTotalMinutes(new DateInterval('PT2H59M59S'), 'floor'));
    }

    /**
     *
     */
    public function testIntervalToHours()
    {
        $this->assertEquals(2, ComparableDateInterval::getTotalHours(new DateInterval('PT2H'), 'round'));
        $this->assertEquals(2, ComparableDateInterval::getTotalHours(new DateInterval('PT2H5M'), 'round'));
        $this->assertEquals(2, ComparableDateInterval::getTotalHours(new DateInterval('PT1H45M'), 'round'));
        $this->assertEquals(2, ComparableDateInterval::getTotalHours(new DateInterval('PT1H1M'), 'ceil'));
        $this->assertEquals(2, ComparableDateInterval::getTotalHours(new DateInterval('PT2H59M'), 'floor'));
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

        $seconds = ComparableDateInterval::getTotalSeconds($interval);
        $this->assertLessThan(0, $seconds);
    }

    public function testInvalidConstructor()
    {
        $this->expectException(Exception::class);
        new ComparableDateInterval('abc123');
    }
}
