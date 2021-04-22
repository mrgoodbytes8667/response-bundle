<?php


namespace Bytes\ResponseBundle\HttpClient\Response;


use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function Symfony\Component\String\u;

/**
 * Class Response
 * @package Bytes\ResponseBundle\HttpClient\Response
 *
 * @experimental
 */
class Response implements ClientResponseInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array|null
     */
    private $deserializeContext = [];

    /**
     * @var callable(static, mixed)|null
     */
    private $onSuccessCallable;

    /**
     * @var bool
     */
    private bool $callbackExecuted = false;

    /**
     * @var mixed|null
     */
    private $results = null;

    /**
     * @var array|null
     */
    private $extraParams = [];

    //region Instantiation

    /**
     * Response constructor.
     * @param SerializerInterface $serializer
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(private SerializerInterface $serializer, private ?EventDispatcherInterface $dispatcher = null)
    {
    }

    /**
     * @param SerializerInterface $serializer
     * @param EventDispatcherInterface|null $dispatcher
     * @return static
     */
    #[Pure]
    public static function make(SerializerInterface $serializer, ?EventDispatcherInterface $dispatcher = null): static
    {
        return new static($serializer, $dispatcher);
    }

    /**
     * @param ClientResponseInterface $clientResponse
     * @param array $params Extra params handed to setExtraParams()
     * @return static
     */
    public static function makeFrom($clientResponse, array $params = []): static
    {
        $static = new static($clientResponse->getSerializer(), $clientResponse->getDispatcher());
        $static->setExtraParams($params);
        return $static;
    }

    /**
     * Method to instantiate the response from the HttpClient
     * @param ResponseInterface $response
     * @param string|null $type Type to deserialize into for deserialize(), can be overloaded by deserialize()
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable(static, mixed)|null $onSuccessCallable If set, should be triggered by deserialize()/callback() on success
     * @return static
     */
    public function withResponse(ResponseInterface $response, ?string $type, array $context = [], ?callable $onSuccessCallable = null): static
    {
        $new = clone $this;
        $new->setResponse($response);
        $new->setType($type);
        $new->setDeserializeContext($context);
        $new->setOnSuccessCallable($onSuccessCallable);

        return $new;
    }
    //endregion

    //region Getters/Setters

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @return EventDispatcherInterface|null
     */
    public function getDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getDeserializeContext(): ?array
    {
        return $this->deserializeContext;
    }

    /**
     * @param array|null $deserializeContext
     * @return $this
     */
    public function setDeserializeContext(?array $deserializeContext): self
    {
        $this->deserializeContext = $deserializeContext;
        return $this;
    }

    /**
     * @return callable(static, mixed)|null
     */
    public function getOnSuccessCallable(): ?callable
    {
        return $this->onSuccessCallable;
    }

    /**
     * @param callable(static, mixed)|null $onSuccessCallable
     * @return $this
     */
    public function setOnSuccessCallable(?callable $onSuccessCallable): self
    {
        $this->onSuccessCallable = $onSuccessCallable;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return array|null
     */
    public function getExtraParams(): ?array
    {
        return $this->extraParams;
    }

    /**
     * For any other parameters that are needed. Saved off by default, but this is meant to be overloaded if needed.
     * @param array $extraParams
     * @return $this
     */
    public function setExtraParams(array $extraParams = []): static
    {
        $this->extraParams = $extraParams;
        return $this;
    }

    //endregion

    //region Methods
    /**
     * @param bool $throw
     * @param array $context
     * @param string|null $type
     * @return mixed
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function deserialize(bool $throw = true, array $context = [], ?string $type = null)
    {
        if (empty($type ?? $this->type)) {
            throw new \InvalidArgumentException(sprintf('The argument "$type" must be provided to %s if the type property is not set.', __METHOD__));
        }
        try {
            $content = $this->response->getContent();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $exception) {
            if ($throw) {
                throw $exception;
            }
            $content = $exception->getResponse()->getContent(false);
            // If we're deserializing into an array, try to deserialize into a single instance instead and wrap it
            $type = $type ?? $this->type;
            if (u($type)->endsWith('[]')) {
                $single = u($type)->beforeLast('[]')->toString();

                return [$this->serializer->deserialize($content, $single, 'json', $context)];
            }
        }
        $this->results = $this->serializer->deserialize($content, $type ?? $this->type, 'json', $context ?: $this->deserializeContext);

        $this->callback();

        return $this->results;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        try {
            return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
        } catch (TransportExceptionInterface) {
            return false;
        }
    }

    /**
     * @param bool $rerunIfAlreadyRun
     * @return $this
     */
    public function callback(bool $rerunIfAlreadyRun = false): self
    {
        if ((!$this->callbackExecuted || ($this->callbackExecuted && $rerunIfAlreadyRun)) && !is_null($this->onSuccessCallable) && is_callable($this->onSuccessCallable) && $this->isSuccess()) {
            $this->callbackExecuted = true;
            call_user_func($this->onSuccessCallable, $this, $this->results);
        }
        return $this;
    }
    //endregion

    //region Response Helpers

    /**
     * Gets the HTTP status code of the response.
     *
     * @return int|null
     *
     * @throws TransportExceptionInterface when a network error occurs
     */
    public function getStatusCode(): ?int
    {
        return $this->response?->getStatusCode();
    }

    /**
     * Gets the HTTP headers of the response.
     *
     * @param bool $throw Whether an exception should be thrown on 3/4/5xx status codes
     *
     * @return string[][]|null The headers of the response keyed by header names in lowercase
     *
     * @throws TransportExceptionInterface   When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface      On a 4xx when $throw is true
     * @throws ServerExceptionInterface      On a 5xx when $throw is true
     */
    public function getHeaders(bool $throw = true): ?array
    {
        return $this->response?->getHeaders($throw);
    }

    /**
     * Gets the response body as a string.
     *
     * @param bool $throw Whether an exception should be thrown on 3/4/5xx status codes
     *
     * @return string|null
     *
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     * @throws TransportExceptionInterface When a network error occurs
     */
    public function getContent(bool $throw = true): ?string
    {
        return $this->response?->getContent($throw);
    }
    //endregion
}