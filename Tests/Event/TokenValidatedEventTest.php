<?php


namespace Bytes\ResponseBundle\Tests\Event;


use Bytes\ResponseBundle\Event\TokenValidatedEvent;
use Bytes\ResponseBundle\Tests\Fixtures\Token;
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

        self::assertEquals($token, $event->getToken());
        self::assertEquals($validation, $event->getValidation());
        self::assertEquals($user, $event->getUser());
        self::assertInstanceOf(TokenValidatedEvent::class, $event->setToken($token2));
        self::assertInstanceOf(TokenValidatedEvent::class, $event->setValidation($validation2));
        self::assertInstanceOf(TokenValidatedEvent::class, $event->setUser($user2));
        self::assertEquals($token2, $event->getToken());
        self::assertEquals($validation2, $event->getValidation());
        self::assertEquals($user2, $event->getUser());
    }

    public function testNewWithUserFromToken()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $token = new Token();
        $token->setUser($user);

        $validation = $this->getMockBuilder(TokenValidationResponseInterface::class)->getMock();

        $event = TokenValidatedEvent::new($token, $validation);
        self::assertEquals($token, $event->getToken());
        self::assertEquals($validation, $event->getValidation());
        self::assertEquals($user, $event->getUser());
    }
}