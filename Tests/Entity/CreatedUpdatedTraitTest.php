<?php

namespace Bytes\ResponseBundle\Tests\Entity;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Entity\CreatedUpdatedTrait;
use PHPUnit\Framework\TestCase;

class CreatedUpdatedTraitTest extends TestCase
{
    use TestFakerTrait;

    /**
     * @dataProvider provideMock
     */
    public function testGetSetCreatedAt(CreatedUpdatedTrait|\PHPUnit\Framework\MockObject\MockObject $mock)
    {
        $now = $this->faker->dateTime();
        self::assertNull($mock->getCreatedAt());
        $mock->setCreatedAt($now);
        self::assertEquals($now, $mock->getCreatedAt());
    }

    /**
     * @dataProvider provideMock
     */
    public function testInitializeDates(CreatedUpdatedTrait|\PHPUnit\Framework\MockObject\MockObject $mock)
    {
        $now = $this->faker->dateTime();
        $mock->initializeDates();
        self::assertEquals($mock->getCreatedAt(), $mock->getUpdatedAt());
        $mock->setCreatedAt($now);
        self::assertEquals($now, $mock->getCreatedAt());
        self::assertNotEquals($now, $mock->getUpdatedAt());
    }

    /**
     * @dataProvider provideMock
     */
    public function testGetSetUpdatedAt(CreatedUpdatedTrait|\PHPUnit\Framework\MockObject\MockObject $mock)
    {
        $now = $this->faker->dateTime();
        self::assertNull($mock->getUpdatedAt());
        $mock->setUpdatedAt($now);
        self::assertEquals($now, $mock->getUpdatedAt());
    }

    public function provideMock()
    {
        yield [$this->getMockForTrait(CreatedUpdatedTrait::class)];
    }
}
