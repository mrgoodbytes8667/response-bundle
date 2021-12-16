<?php

namespace Bytes\ResponseBundle\Tests\Enums;

use Bytes\ResponseBundle\Enums\ContentType;
use Bytes\Tests\Common\TestEnumTrait;
use Bytes\Tests\Common\TestSerializerTrait;
use Generator;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    use TestSerializerTrait, TestEnumTrait;

    /**
     * @dataProvider provideLabelsValuesExtensions
     * @param $label
     * @param $value
     */
    public function testEnum($label, $value, $extension)
    {
        $enum = ContentType::from($value);
        $this->assertEquals($label, $enum->label);
        $this->assertEquals($value, $enum->value);
        $this->assertEquals($extension, $enum->getExtension());

        $enum = ContentType::from($label);
        $this->assertEquals($label, $enum->label);
        $this->assertEquals($value, $enum->value);
        $this->assertEquals($extension, $enum->getExtension());
    }

    /**
     * @dataProvider provideLabelsValuesExtensions
     * @param $label
     * @param $value
     */
    public function testEnumSerialization($label, $value, $extension)
    {
        $serializer = $this->createSerializer();
        $enum = ContentType::from($value);

        $output = $serializer->serialize($enum, 'json');

        $this->assertEquals(json_encode([
            'label' => $label,
            'value' => $value
        ]), $output);
    }

    /**
     * @return Generator
     */
    public function provideLabelsValuesExtensions()
    {
        yield ['label' => 'imageGif', 'value' => 'image/gif', 'extension' => 'gif'];
        yield ['label' => 'imageJpg', 'value' => 'image/jpeg', 'extension' => 'jpg'];
        yield ['label' => 'imagePng', 'value' => 'image/png', 'extension' => 'png'];
        yield ['label' => 'imageWebP', 'value' => 'image/webp', 'extension' => 'webp'];
        yield ['label' => 'json', 'value' => 'application/json', 'extension' => 'json'];
    }
}