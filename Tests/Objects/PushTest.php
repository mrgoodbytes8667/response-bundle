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
        $key = $this->faker->unique()->word();
        $value = $this->faker->unique()->randomElement([$this->faker->unique()->word(), $this->faker->unique()->numberBetween()]);

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

        foreach(range(3, 10) as $count) {

            $arr->push('', key: $this->faker->unique()->word(), empty: false);
            $this->assertIsArray($arr->value());
            $this->assertCount($count, $arr->value());
        }
    }

    /**
     *
     */
    public function testCreatePush()
    {
        $key = $this->faker->unique()->word();
        $value = $this->faker->unique()->randomElement([$this->faker->unique()->word(), $this->faker->unique()->numberBetween()]);

        $array[$this->faker->unique()->word()] = $this->faker->unique()->word();

        $arr = Push::createPush($array, $value, $key);
        $this->assertInstanceOf(Push::class, $arr);

        $this->assertIsArray($arr->value());
        $this->assertCount(2, $arr->value());
        $this->assertArrayHasKey($key, $arr->value());
    }
}
