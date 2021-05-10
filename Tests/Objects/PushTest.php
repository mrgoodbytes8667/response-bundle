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
        $this->assertEquals($value, $arr->getValue($key));

        $arr->push(null);

        $this->assertIsArray($arr->value());
        $this->assertCount(1, $arr->value());

        $arr->push('', empty: false);
        $this->assertIsArray($arr->value());
        $this->assertCount(2, $arr->value());

        $arr->removeKey(0);
        $this->assertIsArray($arr->value());
        $this->assertCount(1, $arr->value());

        foreach(range(2, 10) as $count) {

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

    /**
     *
     */
    public function testCachedReset()
    {
        $options = Push::createPush(value: $this->faker->word(), key: 'hi there');

        $values = $options->value();
        $camels = $options->camel();
        $snakes = $options->snake();

        $this->assertCount(1, $values);
        $this->assertCount(1, $camels);
        $this->assertCount(1, $snakes);

        $this->assertArrayHasKey('hi there', $values);
        $this->assertArrayNotHasKey('hi_there', $values);
        $this->assertArrayNotHasKey('hiThere', $values);

        $this->assertArrayNotHasKey('hi there', $camels);
        $this->assertArrayNotHasKey('hi_there', $camels);
        $this->assertArrayHasKey('hiThere', $camels);

        $this->assertArrayNotHasKey('hi there', $snakes);
        $this->assertArrayHasKey('hi_there', $snakes);
        $this->assertArrayNotHasKey('hiThere', $snakes);

        $options = $options->push(value: $this->faker->word())
            ->push(value: $this->faker->word(), key: 'hello there');

        $values = $options->value();
        $camels = $options->camel();
        $snakes = $options->snake();

        $this->assertCount(3, $values);
        $this->assertCount(3, $camels);
        $this->assertCount(3, $snakes);

        $this->assertArrayHasKey('hello there', $values);
        $this->assertArrayNotHasKey('hello_there', $values);
        $this->assertArrayNotHasKey('helloThere', $values);

        $this->assertArrayNotHasKey('hello there', $camels);
        $this->assertArrayNotHasKey('hello_there', $camels);
        $this->assertArrayHasKey('helloThere', $camels);

        $this->assertArrayNotHasKey('hello there', $snakes);
        $this->assertArrayHasKey('hello_there', $snakes);
        $this->assertArrayNotHasKey('helloThere', $snakes);

        // Get them again with no changes to test/cover the caching
        $values = $options->value();
        $camels = $options->camel();
        $snakes = $options->snake();

        $this->assertCount(3, $values);
        $this->assertCount(3, $camels);
        $this->assertCount(3, $snakes);
    }    /**
 *
 */
    public function testGetValueInvalidKey()
    {
        $key = $this->faker->unique()->word();
        $value = $this->faker->unique()->randomElement([$this->faker->unique()->word(), $this->faker->unique()->numberBetween()]);

        $arr = Push::create();

        $arr->push($value, $key);

        $this->expectException(\InvalidArgumentException::class);
        $arr->getValue($this->faker->unique()->word());
    }
}
