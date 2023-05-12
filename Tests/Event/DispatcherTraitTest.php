<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Tests\Fixtures\Dispatcher;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class DispatcherTraitTest
 * @package Bytes\ResponseBundle\Tests\Event
 */
class DispatcherTraitTest extends TestCase
{
    use TestFakerTrait;

    /**
     *
     */
    public function testDispatcher()
    {
        $mock = new Dispatcher();

        self::assertNotNull($mock);

        $event = new Event();

        self::assertInstanceOf(Event::class, $mock->triggerFakeEvent($event));
    }

    public function testTokenArgDispatches()
    {
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $token->method('getAccessToken')
            ->willReturn($this->faker->randomAlphanumericString());


        $mock = new Dispatcher();

        $results = $mock->dispatchTokenEvents($token);
        self::assertCount(6, $results);
        foreach($results as $i)
        {
            self::assertInstanceOf(Event::class, $i);
        }

        self::assertInstanceOf(Event::class, $mock->dispatchObtainValidToken($this->faker->word(), TokenSource::app, null, []));

        $validation = $this->getMockBuilder(TokenValidationResponseInterface::class)->getMock();

        self::assertInstanceOf(Event::class, $mock->dispatchTokenValidated($token, $validation));
    }
}