<?php


namespace Bytes\ResponseBundle\Tests\Event;


use Bytes\ResponseBundle\Event\TokenRefreshedEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenRefreshedEventTest
 * @package Bytes\ResponseBundle\Tests\Event
 */
class TokenRefreshedEventTest extends TestCase
{
    /**
     *
     */
    public function testGetSetNew()
    {
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $token2 = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $event = TokenRefreshedEvent::new($token);

        $this->assertEquals($token, $event->getToken());
        $this->assertInstanceOf(TokenRefreshedEvent::class, $event->setToken($token2));
        $this->assertEquals($token2, $event->getToken());
    }

    /**
     *
     */
    public function testGetSetNewWithOld()
    {
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $token2 = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $tokenOld = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $tokenOld2 = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $event = TokenRefreshedEvent::new($token, $tokenOld);

        $this->assertEquals($token, $event->getToken());
        $this->assertEquals($tokenOld, $event->getOldToken());
        $this->assertInstanceOf(TokenRefreshedEvent::class, $event->setToken($token2));
        $this->assertInstanceOf(TokenRefreshedEvent::class, $event->setOldToken($tokenOld2));
        $this->assertEquals($token2, $event->getToken());
        $this->assertEquals($tokenOld2, $event->getOldToken());
    }
}
