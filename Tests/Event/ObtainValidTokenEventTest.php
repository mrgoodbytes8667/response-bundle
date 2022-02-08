<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\Common\Faker\Providers\MiscProvider;
use Bytes\Common\Faker\TestFakerTrait;
use Bytes\EnumSerializerBundle\Faker\FakerEnumProvider;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Event\ObtainValidTokenEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Faker\Generator as FakerGenerator;
use Faker\Provider\Address;
use Faker\Provider\Barcode;
use Faker\Provider\Biased;
use Faker\Provider\Color;
use Faker\Provider\Company;
use Faker\Provider\DateTime;
use Faker\Provider\File;
use Faker\Provider\HtmlLorem;
use Faker\Provider\Image;
use Faker\Provider\Internet;
use Faker\Provider\Lorem;
use Faker\Provider\Medical;
use Faker\Provider\Miscellaneous;
use Faker\Provider\Payment;
use Faker\Provider\Person;
use Faker\Provider\PhoneNumber;
use Faker\Provider\Text;
use Faker\Provider\UserAgent;
use Faker\Provider\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ObtainValidTokenEventTest
 * @package Bytes\ResponseBundle\Tests\Event
 *
 * @property FakerGenerator|FakerEnumProvider|MiscProvider|Address|Barcode|Biased|Color|Company|DateTime|File|HtmlLorem|Image|Internet|Lorem|Medical|Miscellaneous|Payment|Person|PhoneNumber|Text|UserAgent|Uuid $faker
 */
class ObtainValidTokenEventTest extends TestCase
{
    use TestFakerTrait;

    /**
     * @var string[]
     */
    protected $providers = [FakerEnumProvider::class];

    /**
     *
     */
    public function testNew()
    {
        $identifier = $this->faker->word();
        $tokenSource = $this->faker->randomEnum(TokenSource::class);
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $scopes = $this->faker->words(3);
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $event = ObtainValidTokenEvent::new($identifier, $tokenSource, $user, $scopes);

        self::assertEquals($identifier, $event->getIdentifier());
        self::assertEquals($tokenSource, $event->getTokenSource());
        self::assertEquals($user, $event->getUser());
        self::assertCount(3, $event->getScopes());
        self::assertNull($event->getToken());

        self::assertInstanceOf(ObtainValidTokenEvent::class, $event->setToken($token));
        self::assertEquals($token, $event->getToken());
    }

    /**
     *
     */
    public function testUserTokenWithNoUser()
    {
        $identifier = $this->faker->word();
        $tokenSource = TokenSource::user;
        $this->expectException(InvalidArgumentException::class);
        $event = ObtainValidTokenEvent::new($identifier, $tokenSource);
    }

    /**
     *
     */
    public function testGetSet()
    {
        $identifier = $this->faker->word();
        $tokenSource = TokenSource::app;
        $identifier2 = $this->faker->word();
        $tokenSource2 = $this->faker->valid(
            function ($value) use ($tokenSource) {
                return !$tokenSource->equals($value);
            })
            ->randomEnum(TokenSource::class);
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $token = $this->getMockBuilder(AccessTokenInterface::class)->getMock();
        $scopes = $this->faker->words(3);
        $event = ObtainValidTokenEvent::new($identifier, $tokenSource);

        self::assertEquals($identifier, $event->getIdentifier());
        self::assertEquals($tokenSource, $event->getTokenSource());
        self::assertNull($event->getUser());
        self::assertNull($event->getToken());

        self::assertInstanceOf(ObtainValidTokenEvent::class, $event->setIdentifier($identifier2));
        self::assertInstanceOf(ObtainValidTokenEvent::class, $event->setTokenSource($tokenSource2));
        self::assertInstanceOf(ObtainValidTokenEvent::class, $event->setUser($user));
        self::assertInstanceOf(ObtainValidTokenEvent::class, $event->setToken($token));
        self::assertEquals($identifier2, $event->getIdentifier());
        self::assertEquals($tokenSource2, $event->getTokenSource());
        self::assertEquals($user, $event->getUser());
        self::assertEquals($token, $event->getToken());

        self::assertCount(0, $event->getScopes());
        self::assertInstanceOf(ObtainValidTokenEvent::class, $event->setScopes($scopes));
        self::assertCount(3, $event->getScopes());

    }
}

