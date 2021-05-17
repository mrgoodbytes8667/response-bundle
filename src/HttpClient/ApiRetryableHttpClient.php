<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Event\ApiRetryEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\AsyncDecoratorTrait;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Response\AsyncResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class ApiRetryableHttpClient
 * RetryableHttpClient that lets you modify the request (for API token renewal)
 * @package Bytes\ResponseBundle\HttpClient
 */
class ApiRetryableHttpClient implements HttpClientInterface
{
    use AsyncDecoratorTrait;

    private $strategy;
    private $maxRetries;
    private $logger;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ApiClientInterface|null
     */
    private $apiClient;

    /**
     * @param int $maxRetries The maximum number of times to retry
     */
    public function __construct(HttpClientInterface $client, RetryStrategyInterface $strategy = null, int $maxRetries = 3, LoggerInterface $logger = null, EventDispatcherInterface $eventDispatcher = null, ApiClientInterface $apiClient = null)
    {
        $this->client = $client;
        $this->strategy = $strategy ?? new GenericRetryStrategy();
        $this->maxRetries = $maxRetries;
        $this->logger = $logger ?? new NullLogger();
        $this->dispatcher = $eventDispatcher;
        $this->apiClient = $apiClient;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if ($this->maxRetries <= 0) {
            return new AsyncResponse($this->client, $method, $url, $options);
        }

        $retryCount = 0;
        $content = '';
        $firstChunk = null;

        return new AsyncResponse($this->client, $method, $url, $options, function (ChunkInterface $chunk, AsyncContext $context) use ($method, $url, $options, &$retryCount, &$content, &$firstChunk) {
            $exception = null;
            try {
                if ($chunk->isTimeout() || null !== $chunk->getInformationalStatus()) {
                    yield $chunk;

                    return;
                }
            } catch (TransportExceptionInterface $exception) {
                // catch TransportExceptionInterface to send it to the strategy
            }
            if (null !== $exception) {
                // always retry request that fail to resolve DNS
                if ('' !== $context->getInfo('primary_ip')) {
                    $shouldRetry = $this->strategy->shouldRetry($context, null, $exception);
                    if (null === $shouldRetry) {
                        throw new \LogicException(sprintf('The "%s::shouldRetry()" method must not return null when called with an exception.', \get_class($this->decider)));
                    }

                    if (false === $shouldRetry) {
                        $context->passthru();
                        if (null !== $firstChunk) {
                            yield $firstChunk;
                            yield $context->createChunk($content);
                            yield $chunk;
                        } else {
                            yield $chunk;
                        }
                        $content = '';

                        return;
                    }
                }
            } elseif ($chunk->isFirst()) {
                if (false === $shouldRetry = $this->strategy->shouldRetry($context, null, null)) {
                    $context->passthru();
                    yield $chunk;

                    return;
                }

                // Body is needed to decide
                if (null === $shouldRetry) {
                    $firstChunk = $chunk;
                    $content = '';

                    return;
                }
            } else {
                $content .= $chunk->getContent();

                if (!$chunk->isLast()) {
                    return;
                }

                if (null === $shouldRetry = $this->strategy->shouldRetry($context, $content, null)) {
                    throw new \LogicException(sprintf('The "%s::shouldRetry()" method must not return null when called with a body.', \get_class($this->strategy)));
                }

                if (false === $shouldRetry) {
                    $context->passthru();
                    yield $firstChunk;
                    yield $context->createChunk($content);
                    $content = '';

                    return;
                }
            }

            /** @var ApiRetryEvent|null $event */
            $event = $this->dispatcher->dispatch(ApiRetryEvent::new(client: $this->apiClient, method: $method, url: $url, options: $options, context: $context, responseContent: $content, retryCount: $retryCount));
            if(!is_null($event)) {
                if (false === $event->getShouldRetry()) {
                    $context->passthru();
                    yield $firstChunk;
                    yield $context->createChunk($content);
                    $content = '';

                    return;
                }
                $options = $event->getOptions();
            }

            $context->getResponse()->cancel();

            $delay = $this->getDelayFromHeader($context->getHeaders()) ?? $this->strategy->getDelay($context, !$exception && $chunk->isLast() ? $content : null, $exception);
            ++$retryCount;

            $this->logger->info('Try #{count} after {delay}ms' . ($exception ? ': ' . $exception->getMessage() : ', status code: ' . $context->getStatusCode()), [
                'count' => $retryCount,
                'delay' => $delay,
            ]);

            $context->setInfo('retry_count', $retryCount);

            $context->replaceRequest($method, $url, $options);
            $context->pause($delay / 1000);

            if ($retryCount >= $this->maxRetries) {
                $context->passthru();
            }
        });
    }

    private function getDelayFromHeader(array $headers): ?int
    {
        if (null !== $after = $headers['retry-after'][0] ?? null) {
            if (is_numeric($after)) {
                return (int)$after * 1000;
            }

            if (false !== $time = strtotime($after)) {
                return max(0, $time - time()) * 1000;
            }
        }

        return null;
    }
}
