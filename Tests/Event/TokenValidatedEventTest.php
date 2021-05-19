<?php


namespace Bytes\ResponseBundle\Tests\Event;


use Bytes\ResponseBundle\Event\TokenValidatedEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user2 = $this->getMockBuilder(UserInterface::class)->getMock();
        $event = TokenValidatedEvent::new($token, $validation, $user);

        $this->assertEquals($token, $event->getToken());
        $this->assertEquals($validation, $event->getValidation());
        $this->assertEquals($user, $event->getUser());
        $this->assertInstanceOf(TokenValidatedEvent::class, $event->setToken($token2));
        $this->assertInstanceOf(TokenValidatedEvent::class, $event->setValidation($validation2));
        $this->assertInstanceOf(TokenValidatedEvent::class, $event->setUser($user2));
        $this->assertEquals($token2, $event->getToken());
        $this->assertEquals($validation2, $event->getValidation());
        $this->assertEquals($user2, $event->getUser());
    }
}