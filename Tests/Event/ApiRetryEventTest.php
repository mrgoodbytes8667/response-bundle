<?php

namespace Bytes\ResponseBundle\Tests\Event;

use Bytes\Common\Faker\Providers\MiscProvider;
use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Enums\HttpMethods;
use Bytes\ResponseBundle\Event\ApiRetryEvent;
use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
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
use Spatie\Enum\Faker\FakerEnumProvider;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Class ApiRetryEventTest
 * @package Bytes\ResponseBundle\Tests\Event
 *
 * @var FakerGenerator|MiscProvider|Address|Barcode|Biased|Color|Company|DateTime|File|HtmlLorem|Image|Internet|Lorem|Medical|Miscellaneous|Payment|Person|PhoneNumber|Text|UserAgent|Uuid|FakerEnumProvider
 */
class ApiRetryEventTest extends TestCase
{
    use TestFakerTrait;

    protected $providers = [FakerEnumProvider::class];

    public function testNew()
    {
        $passthru = function () { return ''; };
        $info = [];
        $response = new MockResponse();
        $context = new AsyncContext($passthru, HttpClient::create(), $response, $info, '', 0);
        $apiClient = $this->getMockBuilder(ApiClientInterface::class)->getMock();

        $method = $this->faker->randomEnum(HttpMethods::class)->value;
        $url = $this->faker->url();
        $content = $this->faker->text();
        $retryCount = $this->faker->randomDigit();
        $options = $this->faker->words(3);
        $retry = ApiRetryEvent::new(client: $apiClient, method: $method, url: $url, options: $options, context: $context, responseContent: $content);
        $this->assertInstanceOf(ApiRetryEvent::class, $retry);

        $retry = ApiRetryEvent::new(client: $apiClient, method: $method, url: $url, options: $options, context: $context, responseContent: $content, retryCount: $retryCount);
        $this->assertInstanceOf(ApiRetryEvent::class, $retry);

        return [
            'retry' => $retry,
            'apiClient' => $apiClient,
            'method' => $method,
            'url' => $url,
            'options' => $options,
            'context' => $context,
            'content' => $content,
            'retryCount' => $retryCount,
        ];
    }

    /**
     * @depends testNew
     * @param array $args = ['retry' => new ApiRetryEvent(), 'apiClient' => new ApiClientInterface(), 'method' => '', 'url' => '', 'options' => [], 'context' => new AsyncContext(), 'content' => '', 'retryCount' => 0]
     */
    public function testGetSet(array $args)
    {
        /** @var ApiRetryEvent $retry */
        extract($args);

        $this->assertEquals($apiClient, $retry->getClient());
        $this->assertEquals($method, $retry->getMethod());
        $this->assertEquals($url, $retry->getUrl());
        $this->assertCount(3, $retry->getOptions());
        $this->assertEquals($context, $retry->getContext());
        $this->assertEquals($content, $retry->getResponseContent());
        $this->assertEquals($retryCount, $retry->getRetryCount());

        extract((array)$this->provideClient());

        $method = $this->faker->randomEnum(HttpMethods::class)->value;
        $url = $this->faker->url();
        $content = $this->faker->text();
        $retryCount = $this->faker->randomDigit();
        $options = $this->faker->words(3);
        $shouldRetry = $this->faker->boolean();

        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setMethod($method));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setUrl($url));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setOptions($options));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setContext($context));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setResponseContent($content));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setRetryCount($retryCount));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setRetryCount($retryCount));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setClient($apiClient));
        $this->assertInstanceOf(ApiRetryEvent::class, $retry->setShouldRetry($shouldRetry));

        $this->assertEquals($apiClient, $retry->getClient());
        $this->assertEquals($method, $retry->getMethod());
        $this->assertEquals($url, $retry->getUrl());
        $this->assertCount(3, $retry->getOptions());
        $this->assertEquals($context, $retry->getContext());
        $this->assertEquals($content, $retry->getResponseContent());
        $this->assertEquals($retryCount, $retry->getRetryCount());
        $this->assertEquals($shouldRetry, $retry->getShouldRetry());


    }

    public function provideClient()
    {
        $passthru = function () { return ''; };
        $info = [];
        $response = new MockResponse();
        $context = new AsyncContext($passthru, HttpClient::create(), $response, $info, '', 0);
        $apiClient = $this->getMockBuilder(ApiClientInterface::class)->getMock();
        yield ['context' => $context, 'apiClient' => $apiClient];
    }
}
