<?php


namespace Bytes\ResponseBundle\Token\Exceptions;


use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class TokenRevokeException
 * Thrown via the HttpClient or Response class when an error occurs revoking a token
 * @package Bytes\ResponseBundle\Token\Exceptions
 */
class TokenRevokeException extends RuntimeException implements ClientExceptionInterface
{
    /**
     * TokenRevokeException constructor.
     * @param ResponseInterface $response
     * @param string|null $message
     * @throws TransportExceptionInterface
     */
    public function __construct(private readonly ResponseInterface $response, ?string $message = 'Token could not be revoked')
    {
        parent::__construct($message, $response?->getStatusCode() ?? 0);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}