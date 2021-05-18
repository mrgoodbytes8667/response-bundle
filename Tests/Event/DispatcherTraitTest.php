<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\ResponseBundle\Tests\Fixtures\Dispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class DispatcherTraitTest
 * @package Bytes\ResponseBundle\Tests\Event
 */
class DispatcherTraitTest extends TestCase
{
    /**
     *
     */
    public function testDispatcher()
    {
        $mock = new Dispatcher();

        $this->assertNotNull($mock);

        $event = new Event();

        $this->assertInstanceOf(Event::class, $mock->triggerFakeEvent($event));
    }
}