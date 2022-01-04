<?php

namespace Bytes\ResponseBundle\Tests\Annotations;

use Bytes\Common\Faker\Providers\MiscProvider;
use Bytes\Common\Faker\TestFakerTrait;
use Bytes\EnumSerializerBundle\Faker\FakerEnumProvider;
use Bytes\ResponseBundle\Annotations\ClientTrait;
use Bytes\ResponseBundle\Enums\TokenSource;
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
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTraitTest
 * @package Bytes\ResponseBundle\Tests\Annotations
 *
 * @property FakerGenerator|MiscProvider|Address|Barcode|Biased|Color|Company|DateTime|File|HtmlLorem|Image|Internet|Lorem|Medical|Miscellaneous|Payment|Person|PhoneNumber|Text|UserAgent|Uuid|FakerEnumProvider $faker
 */
class ClientTraitTest extends TestCase
{
    use TestFakerTrait;

    /**
     *
     */
    public function testGetSet()
    {
        $this->faker->addProvider(new FakerEnumProvider($this->faker));

        $identifier = $this->faker->word();
        $tokenSource = $this->faker->randomEnum(TokenSource::class);
        $tokenSource2 = $this->faker->randomEnum(TokenSource::class);
        $tokenSource2string = $tokenSource2->value;

        $mock = $this->getMockForTrait(ClientTrait::class);

        $this->assertNull($mock->getIdentifier());
        $mock->setIdentifier($identifier);
        $this->assertEquals($identifier, $mock->getIdentifier());

        $this->assertNull($mock->getTokenSource());
        $mock->setTokenSource($tokenSource);
        $this->assertEquals($tokenSource, $mock->getTokenSource());

        $mock->setTokenSource($tokenSource2string);
        $this->assertEquals($tokenSource2, $mock->getTokenSource());
    }
}
