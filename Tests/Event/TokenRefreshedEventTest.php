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

        self::assertEquals($token, $event->getToken());
        self::assertInstanceOf(TokenRefreshedEvent::class, $event->setToken($token2));
        self::assertEquals($token2, $event->getToken());
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

        self::assertEquals($token, $event->getToken());
        self::assertEquals($tokenOld, $event->getOldToken());
        self::assertInstanceOf(TokenRefreshedEvent::class, $event->setToken($token2));
        self::assertInstanceOf(TokenRefreshedEvent::class, $event->setOldToken($tokenOld2));
        self::assertEquals($token2, $event->getToken());
        self::assertEquals($tokenOld2, $event->getOldToken());
    }
}
