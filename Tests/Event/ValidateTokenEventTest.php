<?php


namespace Bytes\ResponseBundle\Tests\Event;


use Bytes\ResponseBundle\Event\ValidateTokenEvent;
use Bytes\ResponseBundle\Tests\Fixtures\Token;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ValidateTokenEventTest
 * @package Bytes\ResponseBundle\Tests\Event
 */
class ValidateTokenEventTest extends TestCase
{
    /**
     *
     */
    public function testGetSetNew()
    {
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $token2 = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $validation2 = $this->getMockBuilder(TokenValidationResponseInterface::class)->getMock();
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user2 = $this->getMockBuilder(UserInterface::class)->getMock();
        $event = ValidateTokenEvent::new($token, $user);

        $this->assertEquals($token, $event->getToken());
        $this->assertNull($event->getValidation());
        $this->assertEquals($user, $event->getUser());
        $this->assertInstanceOf(ValidateTokenEvent::class, $event->setToken($token2));
        $this->assertInstanceOf(ValidateTokenEvent::class, $event->setValidation($validation2));
        $this->assertInstanceOf(ValidateTokenEvent::class, $event->setUser($user2));
        $this->assertEquals($token2, $event->getToken());
        $this->assertEquals($validation2, $event->getValidation());
        $this->assertEquals($user2, $event->getUser());
    }

    public function testNewWithUserFromToken()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $token = new Token();
        $token->setUser($user);

        $validation = $this->getMockBuilder(TokenValidationResponseInterface::class)->getMock();

        $event = ValidateTokenEvent::new($token);
        $this->assertEquals($token, $event->getToken());
        $this->assertNull($event->getValidation());
        $this->assertEquals($user, $event->getUser());
    }
}