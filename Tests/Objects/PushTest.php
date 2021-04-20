<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Objects\Push;
use PHPUnit\Framework\TestCase;

/**
 * Class PushTest
 * @package Bytes\ResponseBundle\Tests\Objects
 */
class PushTest extends TestCase
{
    use TestFakerTrait;

    /**
     *
     */
    public function testCreate()
    {
        $key = $this->faker->word();
        $value = $this->faker->randomElement([$this->faker->word(), $this->faker->numberBetween()]);

        $arr = Push::create();
        $this->assertInstanceOf(Push::class, $arr);

        $this->assertIsArray($arr->value());
        $this->assertEmpty($arr->value());

        $arr->push($value, $key);

        $this->assertIsArray($arr->value());
        $this->assertCount(1, $arr->value());
        $this->assertArrayHasKey($key, $arr->value());

        $arr->push(null);

        $this->assertIsArray($arr->value());
        $this->assertCount(1, $arr->value());

        $arr->push('', empty: false);
        $this->assertIsArray($arr->value());
        $this->assertCount(2, $arr->value());

        $arr->push('', key: $this->faker->word(), empty: false);
        $this->assertIsArray($arr->value());
        $this->assertCount(3, $arr->value());
    }

    /**
     *
     */
    public function testCreatePush()
    {
        $key = $this->faker->word();
        $value = $this->faker->randomElement([$this->faker->word(), $this->faker->numberBetween()]);

        $array[$this->faker->valid(function($v) use ($key) {
            return $v !== $key;
        })->word()] = $this->faker->valid(function($v) use ($value) {
            return $v !== $value;
        })->word();

        $arr = Push::createPush($array, $value, $key);
        $this->assertInstanceOf(Push::class, $arr);

        $this->assertIsArray($arr->value());
        $this->assertCount(2, $arr->value());
        $this->assertArrayHasKey($key, $arr->value());
    }
}
