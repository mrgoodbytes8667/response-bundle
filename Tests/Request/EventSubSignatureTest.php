<?php

namespace Bytes\ResponseBundle\Tests\Request;

use Bytes\ResponseBundle\Tests\Fixtures\Request\EventSubSignature;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class EventSubSignatureTest
 * @package Bytes\TwitchResponseBundle\Tests\Request
 */
class EventSubSignatureTest extends TestCase
{

    /**
     *
     * @var string
     */
    const TWITCH_HUB_SECRET = 'abc123';

    /**
     * @var string
     */
    const TWITCH_MESSAGE_ID = '5AL_vgZc1wZ7ZqDcU6SYRJyw7ijry1tMaM6_CEiS1zo=';

    /**
     * @var string
     */
    const TWITCH_TIMESTAMP = '2020-11-21T03:33:53Z';

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testValidateHubSignature()
    {
        $content = 'this is a test';

        $headers = $this->buildHeaders($content);

        $signature = new EventSubSignature(self::TWITCH_HUB_SECRET);
        self::assertTrue($signature->validateHubSignature($headers, $content, false));
        self::assertFalse($signature->validateHubSignature($headers, 'non-matching-content', false));
        self::assertTrue($signature->validateHubSignature($headers, $content, true));

        $this->expectException(AccessDeniedHttpException::class);
        $signature->validateHubSignature($headers, 'non-matching-content', true);
    }

    /**
     * @param string $content
     * @return HeaderBag
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function buildHeaders(string $content): HeaderBag
    {
        $hash = hash_hmac('sha256', self::TWITCH_MESSAGE_ID . self::TWITCH_TIMESTAMP . $content, self::TWITCH_HUB_SECRET);

        $response = new MockResponse($content, [
            'response_headers' => [
                EventSubSignature::TWITCH_EVENTSUB_MESSAGE_ID => self::TWITCH_MESSAGE_ID,
                EventSubSignature::TWITCH_EVENTSUB_MESSAGE_TIMESTAMP => self::TWITCH_TIMESTAMP,
                EventSubSignature::SIGNATURE_FIELD => 'sha256=' . $hash
            ]
        ]);
        return new HeaderBag($response->getHeaders());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testNoSignatureHeader()
    {
        $content = 'this is a test';

        $headers = $this->buildHeaders($content);
        $headers->remove(EventSubSignature::SIGNATURE_FIELD);

        $signature = new EventSubSignature(self::TWITCH_HUB_SECRET);

        self::assertFalse($signature->validateHubSignature($headers, $content, false));
        $this->expectException(AccessDeniedHttpException::class);
        $signature->validateHubSignature($headers, $content, true);
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        if (!(extension_loaded('hash'))) {
            self::markTestSkipped('The hash extension is not available.');
        }
    }
}