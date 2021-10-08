<?php

namespace Bytes\ResponseBundle\Tests\Enums;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\Tests\Common\TestEnumTrait;
use Bytes\Tests\Common\TestSerializerTrait;
use Generator;
use PHPUnit\Framework\TestCase;

class TokenSourceTest extends TestCase
{
    use TestSerializerTrait, TestEnumTrait;

    /**
     * @dataProvider provideLabelsValues
     * @param $label
     * @param $value
     */
    public function testEnum($label, $value)
    {
        $enum = TokenSource::from($value);
        $this->assertEquals($label, $enum->label);
        $this->assertEquals($value, $enum->value);

        $enum = TokenSource::from($label);
        $this->assertEquals($label, $enum->label);
        $this->assertEquals($value, $enum->value);
    }

    /**
     * @dataProvider provideLabelsValues
     * @param $label
     * @param $value
     */
    public function testEnumSerialization($label, $value)
    {
        $serializer = $this->createSerializer();
        $enum = TokenSource::from($value);

        $output = $serializer->serialize($enum, 'json');

        $this->assertEquals(json_encode([
            'label' => $label,
            'value' => $value
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

    /**
     *
     */
    public function testFormChoices()
    {
        $this->assertEquals([
            'ID' => 'id',
            'User' => 'user',
            'App' => 'app',
        ], TokenSource::formChoices());
    }
}