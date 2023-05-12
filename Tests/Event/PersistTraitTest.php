<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\ResponseBundle\Event\PersistTrait;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class PersistTraitTest extends TestCase
{
    /**
     *
     */
    public function testGetPersist()
    {
        $event = $this->getMockForTrait(PersistTrait::class);
        self::assertTrue($event->getPersist());
        $event->setPersist(false);
        self::assertFalse($event->getPersist());
        $event->setPersist(true);
        self::assertTrue($event->getPersist());
    }
}