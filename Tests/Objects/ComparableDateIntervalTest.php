<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use DateInterval;
use Exception;
use Faker\Factory;
use Generator;
use LogicException;
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
     * @dataProvider provideIntervalCreateArgs
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
     * @dataProvider provideIntervalCreateArgs
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
    public function provideIntervalCreateArgs()
    {
        yield ['PT900S'];
        yield [900];
        yield [new DateInterval('PT900S')];
        yield [ComparableDateInterval::create(900)];
    }

    /**
     *
     */
    public function testLargeIntervals()
    {
        $this->expectException(LogicException::class);

        ComparableDateInterval::getTotalSeconds(new DateInterval("P5YT50S"));
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