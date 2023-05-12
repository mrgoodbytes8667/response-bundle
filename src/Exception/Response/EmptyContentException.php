<?php


namespace Bytes\ResponseBundle\Exception\Response;


use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class EmptyContentException
 * Thrown via the Response class when deserialization is attempted but the content is empty
 * @package Bytes\ResponseBundle\Exception\Response
 */
class EmptyContentException extends RuntimeException implements ClientExceptionInterface
{
    /**
     * EmptyContentException constructor.
     * @param ResponseInterface $response
     * @throws TransportExceptionInterface
     */
    public function __construct(private readonly ResponseInterface $response)
    {
        parent::__construct('Content is empty and cannot be deserialized', $response?->getStatusCode() ?? 0);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}