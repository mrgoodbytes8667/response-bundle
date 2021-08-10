<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\ResponseBundle\Event\EventTrait;
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
        $this->assertTrue($event->getPersist());
        $event->setPersist(false);
        $this->assertFalse($event->getPersist());
        $event->setPersist(true);
        $this->assertTrue($event->getPersist());
    }
}
