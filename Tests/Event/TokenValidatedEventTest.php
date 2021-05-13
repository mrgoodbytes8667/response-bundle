<?php


namespace Bytes\ResponseBundle\Tests\Event;


use Bytes\ResponseBundle\Event\TokenValidatedEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenValidatedEventTest
 * @package Bytes\ResponseBundle\Tests\Event
 */
class TokenValidatedEventTest extends TestCase
{
    /**
     *
     */
    public function testGetSetNew()
    {
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $token2 = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $validation = $this->getMockBuilder(TokenValidationResponseInterface::class)->getMock();
        $validation2 = $this->getMockBuilder(TokenValidationResponseInterface::class)->getMock();
        $event = TokenValidatedEvent::new($token, $validation);

        $this->assertEquals($token, $event->getToken());
        $this->assertEquals($validation, $event->getValidation());
        $this->assertInstanceOf(TokenValidatedEvent::class, $event->setToken($token2));
        $this->assertInstanceOf(TokenValidatedEvent::class, $event->setValidation($validation2));
        $this->assertEquals($token2, $event->getToken());
        $this->assertEquals($validation2, $event->getValidation());
    }
}