<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ApiRetryEvent
 * Can replace the options in a retry-request.
 * Set shouldRetry to false to prevent a retry.
 */
class ApiRetryEvent extends Event
{
    /**
     * ApiRetryEvent constructor.
     */
    public function __construct(private ApiClientInterface $client, private string $method, private string $url, private AsyncContext $context, private array $options = [], private ?string $responseContent = null, private ?int $retryCount = 0, private bool $shouldRetry = true)
    {
        if (empty($retryCount) || $retryCount < 1) {
            $this->retryCount = 0;
        }
    }

    /**
     * @param mixed ...$options = client, method, url, context, [options], [content], retryCount
     *
     * @return static
     */
    public static function new(...$options)
    {
        return new static(...$options);
    }

    public function getClient(): ApiClientInterface
    {
        return $this->client;
    }

    /**
     * @return $this
     */
    public function setClient(ApiClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount ?? 0;
    }

    /**
     * @return $this
     */
    public function setRetryCount(?int $retryCount): self
    {
        $this->retryCount = $retryCount;

        return $this;
    }

    public function getContext(): AsyncContext
    {
        return $this->context;
    }

    /**
     * @return $this
     */
    public function setContext(AsyncContext $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getResponseContent(): ?string
    {
        return $this->responseContent;
    }

    /**
     * @return $this
     */
    public function setResponseContent(?string $responseContent): self
    {
        $this->responseContent = $responseContent;

        return $this;
    }

    public function getShouldRetry(): bool
    {
        return $this->shouldRetry;
    }

    /**
     * @return $this
     */
    public function setShouldRetry(bool $shouldRetry): self
    {
        $this->shouldRetry = $shouldRetry;

        return $this;
    }
}
