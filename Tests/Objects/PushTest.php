<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Objects\Push;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

/**
 * Class PushTest
 * @package Bytes\ResponseBundle\Tests\Objects
 */
class PushTest extends TestCase
{
    use TestFakerTrait, ExpectDeprecationTrait;

    /**
     *
     */
    public function testCreate()
    {
        $key = $this->faker->unique()->word();
        $value = $this->faker->unique()->randomElement([$this->faker->unique()->word(), $this->faker->unique()->numberBetween()]);

        $arr = Push::create();
        $this->assertInstanceOf(Push::class, $arr);

        $this->assertIsArray($arr->toArray());
        $this->assertEmpty($arr->toArray());

        $arr->push($value, $key);

        $this->assertIsArray($arr->toArray());
        $this->assertCount(1, $arr->toArray());
        $this->assertArrayHasKey($key, $arr->toArray());
        $this->assertEquals($value, $arr->getValue($key));

        $arr->push(null);

        $this->assertIsArray($arr->toArray());
        $this->assertCount(1, $arr->toArray());

        $arr->push('', empty: false);
        $this->assertIsArray($arr->toArray());
        $this->assertCount(2, $arr->toArray());

        $arr->removeKey(0);
        $this->assertIsArray($arr->toArray());
        $this->assertCount(1, $arr->toArray());

        foreach (range(2, 10) as $count) {

            $arr->push('', key: $this->faker->unique()->word(), empty: false);
            $this->assertIsArray($arr->toArray());
            $this->assertCount($count, $arr->toArray());
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

        $this->assertIsArray($arr->toArray());
        $this->assertCount(2, $arr->toArray());
        $this->assertArrayHasKey($key, $arr->toArray());
    }

    /**
     *
     */
    public function testCachedReset()
    {
        $options = Push::createPush(value: $this->faker->word(), key: 'hi there');

        $values = $options->toArray();
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

        $values = $options->toArray();
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
        $values = $options->toArray();
        $camels = $options->camel();
        $snakes = $options->snake();

        $this->assertCount(3, $values);
        $this->assertCount(3, $camels);
        $this->assertCount(3, $snakes);
    }

    /**
     *
     */
    public function testGetValueInvalidKey()
    {
        $key = $this->faker->unique()->word();
        $value = $this->faker->unique()->randomElement([$this->faker->unique()->word(), $this->faker->unique()->numberBetween()]);

        $arr = Push::create();

        $arr->push($value, $key);

        $this->expectException(InvalidArgumentException::class);
        $arr->getValue($this->faker->unique()->word());
    }

    /**
     * @group legacy
     */
    public function testGetValue()
    {
        $push = Push::create(['abc' => 123]);
        $this->assertCount(1, $push->toArray());
    }
}
