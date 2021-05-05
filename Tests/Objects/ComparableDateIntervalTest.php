<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use DateInterval;
use Exception;
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
     * @throws Exception
     */
    public function testCompare()
    {
        $interval = ComparableDateInterval::create('PT900S');

        $this->assertEquals(1, $interval->compare(new DateInterval('PT30S')));
        $this->assertEquals(0, $interval->compare(new DateInterval('PT900S')));
        $this->assertEquals(0, $interval->compare(new DateInterval('PT15M')));
        $this->assertEquals(-1, $interval->compare(new DateInterval('PT30M')));

        $testInterval = new DateInterval('PT900S');
        $testInterval->f = 500;
        $this->assertEquals(-1, $interval->compare($testInterval));
    }
}
