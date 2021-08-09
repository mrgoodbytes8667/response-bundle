<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\ResponseBundle\Event\EventTrait;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class EventTraitTest extends TestCase
{
    /**
     *
     */
    public function testPropagation()
    {
        $event = $this->getMockForTrait(EventTrait::class);
        $this->assertFalse($event->isPropagationStopped());
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}