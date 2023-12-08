<?php

namespace Bytes\ResponseBundle\HttpClient\Response;

use Bytes\ResponseBundle\Exception\Response\EmptyContentException;
use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\SerializerInterface;

use function Symfony\Component\String\u;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class Response.
 *
 * @experimental
 */
class Response implements ClientResponseInterface
{
    private \Symfony\Contracts\HttpClient\ResponseInterface $response;

    private ?string $type = null;

    /**
     * @var array|null
     */
    private $deserializeContext = [];

    /**
     * @var callable(static, mixed)|null
     */
    private $onSuccessCallable;

    /**
     * @var callable(static, mixed)[]|null
     */
    protected $onDeserializeCallables = [];

    private bool $callbackExecuted = false;

    /**
     * @var mixed|null
     */
    private $results;

    /**
     * @var array|null
     */
    private $extraParams = [];

    // region Instantiation

    /**
     * Response constructor.
     */
    public function __construct(private readonly SerializerInterface $serializer, private readonly ?EventDispatcherInterface $dispatcher = null, protected bool $throwOnDeserializationWhenContentEmpty = false)
    {
    }

    #[Pure]
    public static function make(SerializerInterface $serializer, EventDispatcherInterface $dispatcher = null): static
    {
        return new static($serializer, $dispatcher);
    }

    /**
     * @param ClientResponseInterface $clientResponse
     * @param array                   $params         Extra params handed to setExtraParams()
     */
    public static function makeFrom($clientResponse, array $params = []): static
    {
        $static = new static($clientResponse->getSerializer(), $clientResponse->getDispatcher());
        $static->setExtraParams($params);

        return $static;
    }

    /**
     * Method to instantiate the response from the HttpClient.
     *
     * @param string|null                  $type                  Type to deserialize into for deserialize(), can be overloaded by deserialize()
     * @param array                        $context               Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable(static, mixed)|null $onDeserializeCallable If set, should be triggered by deserialize() on success, modifies/replaces results
     * @param callable(static, mixed)|null $onSuccessCallable     If set, should be triggered by deserialize()/callback() on success
     */
    public function withResponse(ResponseInterface $response, ?string $type, array $context = [], callable $onDeserializeCallable = null, callable $onSuccessCallable = null): static
    {
        $new = clone $this;
        $new->setResponse($response);
        $new->setType($type);
        $new->setDeserializeContext($context);
        $new->setOnDeserializeCallables($onDeserializeCallable);
        $new->setOnSuccessCallable($onSuccessCallable);

        return $new;
    }

    // endregion

    // region Getters/Setters

    /**
     * For magic extra parameter getters.
     *
     * @return mixed|void|null
     */
    public function __call(string $name, array $arguments)
    {
        if (0 === count($arguments)) {
            $param = u($name)->snake();
            if ($param->startsWith('get_')) {
                $param = $param->after('get_')->camel()->toString();
                if (empty($this->getExtraParams())) {
                    return null;
                }

                if (!array_key_exists($param, $this->getExtraParams())) {
                    return null;
                }

                return $this->getExtraParams()[$param];
            }
        }
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function getDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return $this
     */
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getDeserializeContext(): ?array
    {
        return $this->deserializeContext;
    }

    /**
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
     *
     * @return $this
     */
    public function setOnSuccessCallable(?callable $onSuccessCallable): self
    {
        $this->onSuccessCallable = $onSuccessCallable;

        return $this;
    }

    /**
     * @return callable[]|null
     */
    public function getOnDeserializeCallables(): ?array
    {
        return $this->onDeserializeCallables;
    }

    /**
     * @param callable[]|null $onDeserializeCallables
     *
     * @return $this
     */
    public function setOnDeserializeCallables(callable|array|null $onDeserializeCallables): self
    {
        if (!is_null($onDeserializeCallables) && !is_array($onDeserializeCallables)) {
            $onDeserializeCallables = [$onDeserializeCallables];
        }

        $this->onDeserializeCallables = $onDeserializeCallables ?: [];

        return $this;
    }

    /**
     * @return $this
     */
    public function addOnDeserializeCallable(callable $onDeserializeCallable): self
    {
        if (!in_array($onDeserializeCallable, $this->onDeserializeCallables ?: [])) {
            $this->onDeserializeCallables[] = $onDeserializeCallable;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function prependOnDeserializeCallable(callable $onDeserializeCallable): self
    {
        if (!in_array($onDeserializeCallable, $this->onDeserializeCallables ?: [])) {
            array_unshift($this->onDeserializeCallables, $onDeserializeCallable);
        }

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getResults()
    {
        return $this->results;
    }

    public function getExtraParams(): ?array
    {
        return $this->extraParams;
    }

    /**
     * For any other parameters that are needed. Saved off by default, but this is meant to be overloaded if needed.
     *
     * @return $this
     */
    public function setExtraParams(array $extraParams = []): static
    {
        $this->extraParams = $extraParams;

        return $this;
    }

    public function isThrowOnDeserializationWhenContentEmpty(): bool
    {
        return $this->throwOnDeserializationWhenContentEmpty;
    }

    /**
     * @return $this
     */
    public function setThrowOnDeserializationWhenContentEmpty(bool $throwOnDeserializationWhenContentEmpty): self
    {
        $this->throwOnDeserializationWhenContentEmpty = $throwOnDeserializationWhenContentEmpty;

        return $this;
    }

    // endregion

    // region Methods
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws EmptyContentException
     */
    public function deserialize(bool $throw = true, array $context = [], string $type = null)
    {
        if (empty($type)) {
            $type = $type ?? $this->type;
        }

        if (empty($type)) {
            throw new InvalidArgumentException(sprintf('The argument "$type" must be provided to %s if the type property is not set.', __METHOD__));
        }

        $throw = $this->deserializeGetThrow($throw, $type);
        try {
            $content = $this->response->getContent();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            if ($throw) {
                throw $exception;
            }

            // If our content is empty, we cannot deserialize. Re-throw the original exception.
            $content = $exception->getResponse()->getContent(false);
            if (empty($content)) {
                throw $exception;
            }

            list('continue' => $continue, 'content' => $content) = $this->deserializeOnError($context, $content, $type);
            if (!$continue) {
                return $content;
            }
        }

        if ($this->throwOnDeserializationWhenContentEmpty && empty($content)) {
            throw new EmptyContentException($this->response);
        }

        $this->results = $this->onDeserializeCallback($this->doDeserializeContent($content, $type, $context));

        $this->onSuccessCallback();

        return $this->results;
    }

    public function isSuccess(): bool
    {
        try {
            return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
        } catch (TransportExceptionInterface) {
            return false;
        }
    }

    /**
     * @return $this
     *
     * @deprecated Since 2.0.0, use onSuccessCallback() instead
     */
    public function callback(bool $rerunIfAlreadyRun = false): self
    {
        trigger_deprecation('mrgoodbytes8667/response-bundle', '2.0.0', 'The "%s" method is deprecated, use "onSuccessCallback" instead.', __METHOD__);

        return $this->onSuccessCallback($rerunIfAlreadyRun);
    }

    /**
     * @return $this
     */
    public function onSuccessCallback(bool $rerunIfAlreadyRun = false): self
    {
        if ((!$this->callbackExecuted || ($this->callbackExecuted && $rerunIfAlreadyRun)) && !is_null($this->onSuccessCallable) && is_callable($this->onSuccessCallable) && $this->isSuccess()) {
            $this->callbackExecuted = true;
            call_user_func($this->onSuccessCallable, $this, $this->results);
        }

        return $this;
    }

    /**
     * @return mixed|null
     */
    protected function onDeserializeCallback($results)
    {
        foreach ($this->onDeserializeCallables as $onDeserializeCallable) {
            if (!is_null($onDeserializeCallable) && is_callable($onDeserializeCallable)) {
                $results = call_user_func($onDeserializeCallable, $this, $results);
            }
        }

        return $results;
    }

    // endregion

    // region Response Helpers

    /**
     * Gets the HTTP status code of the response.
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
     * @throws ClientExceptionInterface      On a 4xx when $throw is true
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ServerExceptionInterface      On a 5xx when $throw is true
     * @throws TransportExceptionInterface   When a network error occurs
     */
    public function getContent(bool $throw = true): ?string
    {
        return $this->response?->getContent($throw);
    }

    // endregion

    // region Deserialization Overloads
    /**
     * @return bool
     */
    protected function deserializeGetThrow(bool $throw, ?string $type)
    {
        return $throw;
    }

    /**
     * @return array{continue: bool, content: mixed}
     */
    protected function deserializeOnError(array $context, $content, ?string $type): array
    {
        // If we're deserializing into an array, try to deserialize into a single instance instead and wrap it
        if (u($type)->endsWith('[]')) {
            $single = u($type)->beforeLast('[]')->toString();

            return [
                'continue' => false,
                'content' => [$this->serializer->deserialize($content, $single, 'json', $context)],
            ];
        }

        return [
            'continue' => true,
            'content' => $content,
        ];
    }

    protected function doDeserializeContent(mixed $content, string $type, array $context): mixed
    {
        return $this->serializer->deserialize($content, $type, 'json', $context ?: $this->deserializeContext);
    }

    // endregion
}
