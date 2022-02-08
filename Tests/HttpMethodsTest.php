<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\EnumSerializerBundle\PhpUnit\EnumAssertions;
use Bytes\ResponseBundle\Enums\HttpMethods;
use Generator;
use PHPUnit\Framework\TestCase;

class HttpMethodsTest extends TestCase
{
    /**
     * @dataProvider provideAll
     * @param HttpMethods $enum
     * @param string $value
     * @return void
     */
    public function testAll(HttpMethods $enum, string $value)
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
        foreach (HttpMethods::cases() as $enum) {
            yield $enum->value => ['enum' => $enum, 'value' => $enum->value];
        }
    }
}
