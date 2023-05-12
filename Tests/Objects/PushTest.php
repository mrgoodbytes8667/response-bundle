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
        self::assertInstanceOf(Push::class, $arr);

        self::assertIsArray($arr->toArray());
        self::assertEmpty($arr->toArray());

        $arr->push($value, $key);

        self::assertIsArray($arr->toArray());
        self::assertCount(1, $arr->toArray());
        self::assertArrayHasKey($key, $arr->toArray());
        self::assertEquals($value, $arr->getValue($key));

        $arr->push(null);

        self::assertIsArray($arr->toArray());
        self::assertCount(1, $arr->toArray());

        $arr->push('', empty: false);
        self::assertIsArray($arr->toArray());
        self::assertCount(2, $arr->toArray());

        $arr->removeKey(0);
        self::assertIsArray($arr->toArray());
        self::assertCount(1, $arr->toArray());

        foreach (range(2, 10) as $count) {

            $arr->push('', key: $this->faker->unique()->word(), empty: false);
            self::assertIsArray($arr->toArray());
            self::assertCount($count, $arr->toArray());
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
        self::assertInstanceOf(Push::class, $arr);

        self::assertIsArray($arr->toArray());
        self::assertCount(2, $arr->toArray());
        self::assertArrayHasKey($key, $arr->toArray());
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

        self::assertCount(1, $values);
        self::assertCount(1, $camels);
        self::assertCount(1, $snakes);

        self::assertArrayHasKey('hi there', $values);
        self::assertArrayNotHasKey('hi_there', $values);
        self::assertArrayNotHasKey('hiThere', $values);

        self::assertArrayNotHasKey('hi there', $camels);
        self::assertArrayNotHasKey('hi_there', $camels);
        self::assertArrayHasKey('hiThere', $camels);

        self::assertArrayNotHasKey('hi there', $snakes);
        self::assertArrayHasKey('hi_there', $snakes);
        self::assertArrayNotHasKey('hiThere', $snakes);

        $options = $options->push(value: $this->faker->word())
            ->push(value: $this->faker->word(), key: 'hello there');

        $values = $options->toArray();
        $camels = $options->camel();
        $snakes = $options->snake();

        self::assertCount(3, $values);
        self::assertCount(3, $camels);
        self::assertCount(3, $snakes);

        self::assertArrayHasKey('hello there', $values);
        self::assertArrayNotHasKey('hello_there', $values);
        self::assertArrayNotHasKey('helloThere', $values);

        self::assertArrayNotHasKey('hello there', $camels);
        self::assertArrayNotHasKey('hello_there', $camels);
        self::assertArrayHasKey('helloThere', $camels);

        self::assertArrayNotHasKey('hello there', $snakes);
        self::assertArrayHasKey('hello_there', $snakes);
        self::assertArrayNotHasKey('helloThere', $snakes);

        // Get them again with no changes to test/cover the caching
        $values = $options->toArray();
        $camels = $options->camel();
        $snakes = $options->snake();

        self::assertCount(3, $values);
        self::assertCount(3, $camels);
        self::assertCount(3, $snakes);
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
        self::assertCount(1, $push->toArray());
    }
}
