<?php

namespace Bytes\ResponseBundle\Tests\Enums;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\Tests\Common\TestEnumTrait;
use Bytes\Tests\Common\TestSerializerTrait;
use Generator;
use PHPUnit\Framework\TestCase;

class TokenSourceTest extends TestCase
{
    use TestSerializerTrait;
    use TestEnumTrait;

    /**
     * @dataProvider provideLabelsValues
     */
    public function testEnum($label, $value)
    {
        $enum = TokenSource::from($value);
        self::assertEquals($value, $enum->value);
    }

    /**
     * @dataProvider provideLabelsValues
     */
    public function testEnumSerialization($label, $value)
    {
        $serializer = $this->createSerializer();
        $enum = TokenSource::from($value);

        $output = $serializer->serialize($enum, 'json');

        self::assertEquals(json_encode([
            'label' => $label,
            'value' => $value,
        ]), $output);
    }

    /**
     * @return Generator
     */
    public function provideLabelsValues()
    {
        yield ['label' => 'app', 'value' => 'app'];
        yield ['label' => 'id', 'value' => 'id'];
        yield ['label' => 'user', 'value' => 'user'];
    }

    public function testFormChoices()
    {
        self::assertEquals([
            'ID' => 'id',
            'User' => 'user',
            'App' => 'app',
        ], TokenSource::formChoices());
    }
}
