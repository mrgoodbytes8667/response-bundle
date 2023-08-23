<?php


namespace Bytes\ResponseBundle\Interfaces;


use Bytes\ResponseBundle\Exception\Response\EmptyContentException;
use InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @experimental
 *
 * @template TDeserializationType
 */
interface ClientResponseInterface
{
    //region Instantiation

    /**
     * @return static
     */
    public static function make(SerializerInterface $serializer, ?EventDispatcherInterface $dispatcher = null);

    /**
     * @param ClientResponseInterface $clientResponse
     * @param array $params Extra params handed to setExtraParams()
     * @return static
     */
    public static function makeFrom($clientResponse, array $params = []);

    /**
     * @return $this
     */
    public function setExtraParams(array $params = []);

    /**
     * Method to instantiate the response from the HttpClient
     * @param class-string<TDeserializationType>|null $type Type to deserialize into for deserialize(), can be overloaded by deserialize()
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable|null $onDeserializeCallable If set, should be triggered by deserialize() on success, modifies/replaces results
     * @param callable|null $onSuccessCallable If set, should be triggered by deserialize() on success
     * @return static
     */
    public function withResponse(ResponseInterface $response, ?string $type, array $context = [], ?callable $onDeserializeCallable = null, ?callable $onSuccessCallable = null);

    /**
     * @return $this
     */
    public function onSuccessCallback(bool $rerunIfAlreadyRun = false);
    
    //endregion

    //region Getters/Setters

    public function getSerializer(): SerializerInterface;

    /**
     * @return class-string<TDeserializationType>|null
     */
    public function getType(): ?string;

    /**
     * @param class-string<TDeserializationType>|null $type
     * @return $this
     */
    public function setType(?string $type);

    public function getResponse(): ResponseInterface;

    /**
     * @return $this
     */
    public function setResponse(ResponseInterface $response);

    public function getDeserializeContext(): ?array;

    /**
     * @return $this
     */
    public function setDeserializeContext(?array $deserializeContext);

    public function getOnSuccessCallable(): ?callable;

    /**
     * @return $this
     */
    public function setOnSuccessCallable(?callable $onSuccessCallable);
    
    //endregion

    /**
     * @param class-string<TDeserializationType>|null $type
     *
     * @return TDeserializationType

     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws EmptyContentException
     */
    public function deserialize(bool $throw = true, array $context = [], ?string $type = null);

    public function isSuccess(): bool;

    //region Response Helpers

    /**
     * Gets the HTTP status code of the response.
     *
     *
     * @throws TransportExceptionInterface when a network error occurs
     */
    public function getStatusCode(): ?int;

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
    public function getHeaders(bool $throw = true): ?array;

    /**
     * Gets the response body as a string.
     *
     * @param bool $throw Whether an exception should be thrown on 3/4/5xx status codes
     *
     *
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     * @throws TransportExceptionInterface When a network error occurs
     */
    public function getContent(bool $throw = true): ?string;
    
    //endregion
}