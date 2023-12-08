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
     */
    public function testAll(DayOfWeek $enum, int $value): void
    {
        EnumAssertions::assertIsEnum($enum);
        EnumAssertions::assertEqualsEnum($enum, $value);
        EnumAssertions::assertSameEnumValue($enum, $value);
    }

    public function testAbbreviations(): void
    {
        $all = DayOfWeek::provideAbbreviatedFormChoices();
        self::assertArrayHasKey('Su', $all);
        $sunday = array_shift($all);
        self::assertEquals(0, $sunday);
    }

    public function provideAll(): Generator
    {
        foreach (DayOfWeek::cases() as $enum) {
            yield $enum->value => ['enum' => $enum, 'value' => $enum->value];
        }
    }

    /**
     * @dataProvider provideAll
     */
    public function testTryFromSuccessful(DayOfWeek $enum, int $value): void
    {
        EnumAssertions::assertSameEnum($enum, DayOfWeek::tryFrom($value));
    }

    public function testTryFromUnsuccessful(): void
    {
        self::assertNull(DayOfWeek::tryFrom(-1));
        self::assertNull(DayOfWeek::tryFrom(7));
        self::assertNull(DayOfWeek::tryFrom(8));
    }
}
