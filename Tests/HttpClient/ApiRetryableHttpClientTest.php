<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2018-2021 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Bytes\ResponseBundle\Tests\HttpClient;

use Bytes\ResponseBundle\Event\ApiRetryEvent;
use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
use Bytes\ResponseBundle\HttpClient\ApiRetryableHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ApiRetryableHttpClientTest
 * Based on RetryableHttpClientTest from https://github.com/symfony/http-client/blob/625caf0b0aa516463f14415e24f12fbe25861dd9/Tests/RetryableHttpClientTest.php.
 */
class ApiRetryableHttpClientTest extends TestCase
{
    public function testRetryOnError()
    {
        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('', ['http_code' => 500]),
                new MockResponse('', ['http_code' => 200]),
            ]),
            new GenericRetryStrategy([500], 0),
            1, eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        self::assertSame(200, $response->getStatusCode());
    }

    public function testRetryRespectStrategy()
    {
        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('', ['http_code' => 500]),
                new MockResponse('', ['http_code' => 500]),
                new MockResponse('', ['http_code' => 200]),
            ]),
            new GenericRetryStrategy([500], 0),
            1, eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        $this->expectException(ServerException::class);
        $response->getHeaders();
    }

    public function testRetryWithBody()
    {
        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('', ['http_code' => 500]),
                new MockResponse('', ['http_code' => 200]),
            ]),
            new class(GenericRetryStrategy::DEFAULT_RETRY_STATUS_CODES, 0) extends GenericRetryStrategy {
                public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
                {
                    return null === $responseContent ? null : 200 !== $context->getStatusCode();
                }
            },
            1, eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        self::assertSame(200, $response->getStatusCode());
    }

    public function testRetryWithBodyKeepContent()
    {
        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('my bad', ['http_code' => 400]),
            ]),
            new class([400], 0) extends GenericRetryStrategy {
                public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
                {
                    if (null === $responseContent) {
                        return null;
                    }

                    return 'my bad' !== $responseContent;
                }
            },
            1, eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('my bad', $response->getContent(false));
    }

    public function testRetryWithBodyInvalid()
    {
        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('', ['http_code' => 500]),
                new MockResponse('', ['http_code' => 200]),
            ]),
            new class(GenericRetryStrategy::DEFAULT_RETRY_STATUS_CODES, 0) extends GenericRetryStrategy {
                public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
                {
                    return null;
                }
            },
            1, eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        $this->expectExceptionMessageMatches('/must not return null when called with a body/');
        $response->getHeaders();
    }

    public function testStreamNoRetry()
    {
        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('', ['http_code' => 500]),
            ]),
            new GenericRetryStrategy([500], 0),
            0, eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        foreach ($client->stream($response) as $responseStream) {
            if ($responseStream->isFirst()) {
                self::assertSame(500, $response->getStatusCode());
            }
        }
    }

    public function testRetryWithDnsIssue()
    {
        $client = new ApiRetryableHttpClient(
            new NativeHttpClient(),
            new class(GenericRetryStrategy::DEFAULT_RETRY_STATUS_CODES, 0) extends GenericRetryStrategy {
                public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
                {
                    $this->fail('should not be called');
                }
            },
            2,
            $logger = new TestLogger(), eventDispatcher: new EventDispatcher()
        );

        $response = $client->request('GET', 'http://does.not.exists/foo-bar');

        try {
            $response->getHeaders();
        } catch (TransportExceptionInterface $e) {
            self::assertSame('Could not resolve host "does.not.exists".', $e->getMessage());
        }

        self::assertCount(2, $logger->logs);
        self::assertSame('Try #{count} after {delay}ms: Could not resolve host "does.not.exists".', $logger->logs[0]);
    }

    public function testRetryEvent()
    {
        $passthru = function () { return ''; };
        $info = [];
        $response = new MockResponse();
        $context = new AsyncContext($passthru, HttpClient::create(), $response, $info, '', 0);
        $apiClient = $this->getMockBuilder(ApiClientInterface::class)->getMock();
        $dispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $dispatcher->method('dispatch')->willReturn(new ApiRetryEvent($apiClient, '', '', $context, [], '', 0));

        $client = new ApiRetryableHttpClient(
            new MockHttpClient([
                new MockResponse('', ['http_code' => 500]),
                new MockResponse('', ['http_code' => 200]),
            ]),
            new GenericRetryStrategy([500], 0),
            1, eventDispatcher: $dispatcher, apiClient: $apiClient
        );

        $response = $client->request('GET', 'http://example.com/foo-bar');

        self::assertSame(200, $response->getStatusCode());
    }
}
