<?php

namespace Bytes\ResponseBundle\Tests\Enums;

use Bytes\EnumSerializerBundle\PhpUnit\EnumAssertions;
use Bytes\ResponseBundle\Enums\DayOfWeek;
use Generator;
use PHPUnit\Framework\TestCase;

class DayOfWeekTest extends TestCase
{
    /**
     * @dataProvider provideAll
     * @param DayOfWeek $enum
     * @param int $value
     * @return void
     */
    public function testAll(DayOfWeek $enum, int $value): void
    {
        EnumAssertions::assertIsEnum($enum);
        EnumAssertions::assertEqualsEnum($enum, $value);
        EnumAssertions::assertSameEnumValue($enum, $value);
    }

    /**
     * @return Generator
     */
    public function provideAll(): Generator
    {
        foreach (DayOfWeek::cases() as $enum) {
            yield $enum->value => ['enum' => $enum, 'value' => $enum->value];
        }
    }

    /**
     * @dataProvider provideAll
     * @param DayOfWeek $enum
     * @param int $value
     * @return void
     */
    public function testTryFromSuccessful(DayOfWeek $enum, int $value): void
    {
        EnumAssertions::assertSameEnum($enum, DayOfWeek::tryFrom($value));
    }

    /**
     * @return void
     */
    public function testTryFromUnsuccessful(): void
    {
        $this->assertNull(DayOfWeek::tryFrom(7));
        $this->assertNull(DayOfWeek::tryFrom(8));
    }
}
