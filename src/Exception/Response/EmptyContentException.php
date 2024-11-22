<?php

namespace Bytes\ResponseBundle\Exception\Response;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Thrown via the Response class when deserialization is attempted but the content is empty.
 */
class EmptyContentException extends RuntimeException implements ClientExceptionInterface
{
    /**
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
