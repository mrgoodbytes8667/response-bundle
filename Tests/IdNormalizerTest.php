<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Interfaces\IdInterface;
use Bytes\ResponseBundle\Objects\IdNormalizer;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class IdNormalizerTest.
 */
class IdNormalizerTest extends TestCase
{
    use TestFakerTrait;

    /**
     * @dataProvider provideValidIds
     */
    public function testNormalizeIdArgument($object, $id)
    {
        $message = $this->faker->sentence();
        $result = IdNormalizer::normalizeIdArgument($object, $message, true);

        self::assertEquals($id, $result);
    }

    public function testNormalizeIdInt()
    {
        $message = $this->faker->sentence();
        $result = IdNormalizer::normalizeIdArgument(123, $message, false);

        self::assertEquals('123', $result);
    }

    /**
     * @dataProvider provideIdsForDisallowNulls
     */
    public function testNormalizeIdArgumentAllowNullWithNull($input)
    {
        $message = $this->faker->sentence();
        $result = IdNormalizer::normalizeIdArgument($input, $message, true);

        self::assertNull($result);
    }

    /**
     * @return Generator
     */
    public function provideIdsForDisallowNulls()
    {
        yield [null];

        $object = $this
            ->getMockBuilder(IdInterface::class)
            ->getMock();
        $object->method('getId')
            ->willReturn(null);

        yield [$object];
    }

    /**
     * @dataProvider provideIdsForDisallowNulls
     */
    public function testNormalizeIdArgumentDisallowNull($input)
    {
        $message = $this->faker->sentence();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $result = IdNormalizer::normalizeIdArgument($input, $message);

        self::assertNull($result);
    }

    public function testNormalizeIdArgumentMissingMessageObject()
    {
        $object = $this
            ->getMockBuilder(IdInterface::class)
            ->getMock();
        $object->method('getId')
            ->willReturn(null);

        $result = IdNormalizer::normalizeIdArgument($object, '', true);

        self::assertNull($result);
    }

    public function testNormalizeIdArgumentMissingMessageString()
    {
        $result = IdNormalizer::normalizeIdArgument('', '', true);

        self::assertEmpty($result);
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
