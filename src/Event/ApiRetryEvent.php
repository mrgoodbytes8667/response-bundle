<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Contracts\EventDispatcher\Event;


/**
 * Class ApiRetryEvent
 * Can replace the options in a retry-request.
 * Set shouldRetry to false to prevent a retry.
 * @package Bytes\ResponseBundle\Event
 */
class ApiRetryEvent extends Event
{
    /**
     * ApiRetryEvent constructor.
     * @param ApiClientInterface $client
     * @param string $method
     * @param string $url
     * @param array $options
     * @param int|null $retryCount
     */
    public function __construct(private ApiClientInterface $client, private string $method, private string $url, private AsyncContext $context, private array $options = [], private ?string $responseContent = null, private ?int $retryCount = 0, private bool $shouldRetry = true)
    {
        if(empty($retryCount) || $retryCount < 1)
        {
            $this->retryCount = 0;
        }
    }

    /**
     * @param mixed ...$options = client, method, url, context, [options], [content], retryCount
     * @return static
     */
    public static function new(array ...$options)
    {
        return new static(...$options);
    }

    /**
     * @return ApiClientInterface
     */
    public function getClient(): ApiClientInterface
    {
        return $this->client;
    }

    /**
     * @param ApiClientInterface $client
     * @return $this
     */
    public function setClient(ApiClientInterface $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return int
     */
    public function getRetryCount(): int
    {
        return $this->retryCount ?? 0;
    }

    /**
     * @param int|null $retryCount
     * @return $this
     */
    public function setRetryCount(?int $retryCount): self
    {
        $this->retryCount = $retryCount;
        return $this;
    }

    /**
     * @return AsyncContext
     */
    public function getContext(): AsyncContext
    {
        return $this->context;
    }

    /**
     * @param AsyncContext $context
     * @return $this
     */
    public function setContext(AsyncContext $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getResponseContent(): ?string
    {
        return $this->responseContent;
    }

    /**
     * @param string|null $responseContent
     * @return $this
     */
    public function setResponseContent(?string $responseContent): self
    {
        $this->responseContent = $responseContent;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShouldRetry(): bool
    {
        return $this->shouldRetry;
    }

    /**
     * @param bool $shouldRetry
     * @return $this
     */
    public function setShouldRetry(bool $shouldRetry): self
    {
        $this->shouldRetry = $shouldRetry;
        return $this;
    }

}