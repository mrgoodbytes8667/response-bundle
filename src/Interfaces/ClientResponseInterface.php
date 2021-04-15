<?php


namespace Bytes\ResponseBundle\Interfaces;


use Bytes\ResponseBundle\HttpClient\Response\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Interface ClientResponseInterface
 * @package Bytes\ResponseBundle\Interfaces
 *
 * @experimental
 */
interface ClientResponseInterface
{
    //region Instantiation

    /**
     * @param SerializerInterface $serializer
     * @return static
     */
    public static function make(SerializerInterface $serializer);

    /**
     * @param ClientResponseInterface $clientResponse
     * @param array $params Extra params handed to setExtraParams()
     * @return static
     */
    public static function makeFrom($clientResponse, array $params = []);

    /**
     * @param array $params
     * @return $this
     */
    public function setExtraParams(array $params = []);

    /**
     * Method to instantiate the response from the HttpClient
     * @param ResponseInterface $response
     * @param string|null $type Type to deserialize into for deserialize(), can be overloaded by deserialize()
     * @param array $context Additional context for deserialize(), can be overloaded by deserialize()
     * @param callable|null $onSuccessCallable If set, should be triggered by deserialize() on success
     * @return static
     */
    public function withResponse(ResponseInterface $response, ?string $type, array $context = [], ?callable $onSuccessCallable = null);
    //endregion

    //region Getters/Setters

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $type
     * @return $this
     */
    public function setType(?string $type);

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response);

    /**
     * @return array|null
     */
    public function getDeserializeContext(): ?array;

    /**
     * @param array|null $deserializeContext
     * @return $this
     */
    public function setDeserializeContext(?array $deserializeContext);

    /**
     * @return callable|null
     */
    public function getOnSuccessCallable(): ?callable;

    /**
     * @param callable|null $onSuccessCallable
     * @return $this
     */
    public function setOnSuccessCallable(?callable $onSuccessCallable);
    //endregion

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
    public function deserialize(bool $throw = true, array $context = [], ?string $type = null);

    /**
     * @return bool
     */
    public function isSuccess(): bool;

    //region Response Helpers

    /**
     * Gets the HTTP status code of the response.
     *
     * @return int|null
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
     * @return string|null
     *
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     * @throws TransportExceptionInterface When a network error occurs
     */
    public function getContent(bool $throw = true): ?string;
    //endregion
}