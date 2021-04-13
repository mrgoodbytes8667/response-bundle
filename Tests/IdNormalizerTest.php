<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Interfaces\IdInterface;
use Bytes\ResponseBundle\Objects\IdNormalizer;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class IdNormalizerTest
 * @package Bytes\ResponseBundle\Tests
 */
class IdNormalizerTest extends TestCase
{
    use TestFakerTrait;

    /**
     * @dataProvider provideValidIds
     * @param $object
     * @param $id
     */
    public function testNormalizeIdArgument($object, $id)
    {
        $message = $this->faker->sentence();
        $result = IdNormalizer::normalizeIdArgument($object, $message, true);

        $this->assertEquals($id, $result);
    }

    /**
     * @dataProvider provideIdsForDisallowNulls
     * @param $input
     */
    public function testNormalizeIdArgumentAllowNullWithNull($input)
    {
        $message = $this->faker->sentence();
        $result = IdNormalizer::normalizeIdArgument($input, $message, true);

        $this->assertNull($result);
    }

    /**
     * @return Generator
     */
    public function provideIdsForDisallowNulls()
    {
        yield [null];
    }

    /**
     * @dataProvider provideIdsForDisallowNulls
     * @param $input
     */
    public function testNormalizeIdArgumentDisallowNull($input)
    {
        $message = $this->faker->sentence();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $result = IdNormalizer::normalizeIdArgument($input, $message);

        $this->assertNull($result);
    }

    /**
     *
     */
    public function testNormalizeIdArgumentMissingMessageString()
    {
        $result = IdNormalizer::normalizeIdArgument('', '', true);

        $this->assertEmpty($result);
    }

    /**
     * @return Generator
     */
    public function provideValidIds()
    {
        $this->setupFaker();
        $id = (string) $this->faker->numberBetween(1000, 9999);
        $object = $this
            ->getMockBuilder(IdInterface::class)
            ->getMock();
        $object->method('getId')
            ->willReturn($id);

        yield ['object' => $object, 'id' => $id];
    }
}
