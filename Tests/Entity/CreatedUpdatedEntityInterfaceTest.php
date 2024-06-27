<?php

namespace Bytes\ResponseBundle\Tests\Entity;

use Bytes\ResponseBundle\Entity\CreatedUpdatedEntityInterface;
use Bytes\ResponseBundle\Tests\Fixtures\Entity\CreatedUpdatedEntity;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

/**
 * @see CreatedUpdatedEntityInterface
 */
class CreatedUpdatedEntityInterfaceTest extends TestCase
{
    use ClockSensitiveTrait;

    /**
     * @dataProvider provideMock
     *
     * @param DateTimeInterface $now
     */
    public function testGetSetCreatedAt(CreatedUpdatedEntityInterface $mock, $now)
    {
        self::assertNotNull($mock->getCreatedAt());
        $mock->setCreatedAt(null);
        self::assertNotNull($mock->getCreatedAt());
        $mock->setCreatedAt($now);
        self::assertEquals($now, $mock->getCreatedAt());
    }

    /**
     * @dataProvider provideMock
     *
     * @param DateTimeInterface $now
     */
    public function testGetSetUpdatedAt(CreatedUpdatedEntityInterface $mock, $now)
    {
        self::assertNotNull($mock->getUpdatedAt());
        $mock->setUpdatedAt(null);
        self::assertNotNull($mock->getUpdatedAt());
        $mock->setUpdatedAt($now);
        self::assertEquals($now, $mock->getUpdatedAt());
    }

    public function provideMock()
    {
        yield ['entity' => new CreatedUpdatedEntity(), 'now' => static::mockTime()->now()];
    }
}
