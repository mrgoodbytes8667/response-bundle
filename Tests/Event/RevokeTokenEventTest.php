<?php


namespace Bytes\ResponseBundle\Tests\Event;


use Bytes\ResponseBundle\Event\RevokeTokenEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RevokeTokenEventTest
 * Covers all AbstractTokenEvent children
 * @package Bytes\ResponseBundle\Tests\Event
 */
class RevokeTokenEventTest extends TestCase
{
    /**
     *
     */
    public function testGetSetNew()
    {
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $token2 = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $event = RevokeTokenEvent::new($token);

        $this->assertEquals($token, $event->getToken());
        $this->assertInstanceOf(RevokeTokenEvent::class, $event->setToken($token2));
        $this->assertEquals($token2, $event->getToken());
    }
}
