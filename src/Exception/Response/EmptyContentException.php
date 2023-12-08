<?php

namespace Bytes\ResponseBundle\Exception\Response;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class EmptyContentException
 * Thrown via the Response class when deserialization is attempted but the content is empty.
 */
class EmptyContentException extends RuntimeException implements ClientExceptionInterface
{
    /**
     * EmptyContentException constructor.
     *
     * @throws TransportExceptionInterface
     */
    public function __construct(private readonly ResponseInterface $response)
    {
        parent::__construct('Content is empty and cannot be deserialized', $response?->getStatusCode() ?? 0);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
