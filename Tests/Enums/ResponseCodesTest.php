<?php

namespace Bytes\ResponseBundle\Tests\Enums;

use Bytes\EnumSerializerBundle\PhpUnit\EnumAssertions;
use Bytes\ResponseBundle\Enums\ResponseCodes;
use Generator;
use PHPUnit\Framework\TestCase;

class ResponseCodesTest extends TestCase
{
    /**
     * @dataProvider provideAll
     * @param ResponseCodes $enum
     * @param int $value
     * @return void
     */
    public function testAll(ResponseCodes $enum, int $value)
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
        foreach (ResponseCodes::cases() as $enum) {
            yield $enum->value => ['enum' => $enum, 'value' => $enum->value];
        }
    }
}