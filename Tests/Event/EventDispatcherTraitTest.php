<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\ResponseBundle\Event\EventDispatcherTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class EventDispatcherTraitTest
 * @package Bytes\ResponseBundle\Tests\Event
 */
class EventDispatcherTraitTest extends TestCase
{
    /**
     *
     */
    public function testSetDispatcher()
    {
        $mock = $this->getMockForTrait(EventDispatcherTrait::class);

        self::assertNotNull($mock->setDispatcher(new EventDispatcher()));
    }
}
