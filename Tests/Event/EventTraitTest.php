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
        self::assertFalse($event->isPropagationStopped());
        $event->stopPropagation();
        self::assertTrue($event->isPropagationStopped());
    }
}