<?php

namespace Bytes\ResponseBundle\Tests\Enums;

use BadMethodCallException;
use Bytes\ResponseBundle\Enums\TokenStatus;
use Bytes\Tests\Common\TestEnumTrait;
use Bytes\Tests\Common\TestSerializerTrait;
use Faker\Factory;
use Generator;
use PHPUnit\Framework\TestCase;
use ValueError;

class TokenStatusTest extends TestCase
{
    use TestSerializerTrait, TestEnumTrait;

    /**
     * @dataProvider provideActiveLabelsValues
     * @dataProvider provideInactiveLabelsValues
     * @param $label
     * @param $value
     */
    public function testEnum($label, $value)
    {
        $enum = TokenStatus::from($value);
        self::assertEquals($value, $enum->value);
    }

    /**
     * @dataProvider provideActiveLabelsValues
     * @dataProvider provideInactiveLabelsValues
     * @param $label
     * @param $value
     */
    public function testEnumSerialization($label, $value)
    {
        $serializer = $this->createSerializer();
        $enum = TokenStatus::from($value);

        $output = $serializer->serialize($enum, 'json');

        self::assertEquals(json_encode([
            'label' => $label,
            'value' => $value
        ]), $output);
    }

    /**
     * @return Generator
     */
    public function provideActiveLabelsValues()
    {
        yield ['label' => 'granted', 'value' => 'granted'];
        yield ['label' => 'refreshed', 'value' => 'refreshed'];
    }

    /**
     * @return Generator
     */
    public function provideInactiveLabelsValues()
    {
        yield ['label' => 'expired', 'value' => 'expired'];
        yield ['label' => 'revoked', 'value' => 'revoked'];
    }

    /**
     * @return Generator
     */
    public function provideInvalidLabelsValues()
    {
        $faker = Factory::create();
        yield ['label' => $faker->unique()->word(), 'value' => $faker->unique()->word()];
    }

    /**
     * @dataProvider provideActiveLabelsValues
     * @param $label
     * @param $value
     */
    public function testIsActive($label, $value)
    {
        self::assertTrue(TokenStatus::isActive($label));
        self::assertTrue(TokenStatus::isActive(TokenStatus::from($label)));
        self::assertTrue(TokenStatus::isActive($value));
        self::assertTrue(TokenStatus::isActive(TokenStatus::from($value)));
    }

    /**
     * @dataProvider provideInactiveLabelsValues
     * @param $label
     * @param $value
     */
    public function testIsNotActive($label, $value)
    {
        self::assertFalse(TokenStatus::isActive($label));
        self::assertFalse(TokenStatus::isActive(TokenStatus::from($label)));
        self::assertFalse(TokenStatus::isActive($value));
        self::assertFalse(TokenStatus::isActive(TokenStatus::from($value)));
    }

    /**
     * @dataProvider provideInactiveLabelsValues
     * @dataProvider provideInvalidLabelsValues
     * @param $label
     * @param $value
     */
    public function testIsNotActiveInvalid($label, $value)
    {
        self::assertFalse(TokenStatus::isActive($label));
        self::assertFalse(TokenStatus::isActive($value));
    }

    /**
     * @dataProvider provideInvalidLabelsValues
     * @param $label
     * @param $value
     */
    public function testMakeInvalidValue($label, $value)
    {
        $this->expectException(ValueError::class);
        self::assertFalse(TokenStatus::isActive(TokenStatus::from($value)));
    }

    /**
     *
     */
    public function testFormChoices()
    {
        self::assertEquals([
            'Granted' => 'granted',
            'Refreshed' => 'refreshed',
            'Expired' => 'expired',
            'Revoked' => 'revoked',
        ], TokenStatus::provideFormChoices());
    }
}