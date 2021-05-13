<?php


namespace Bytes\ResponseBundle\Test;


use Bytes\ResponseBundle\Interfaces\ClientResponseInterface;
use Bytes\Tests\Common\Constraint\ResponseContentSame;
use Bytes\Tests\Common\Constraint\ResponseStatusCodeSame;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpFoundation\Test\Constraint as ResponseConstraint;

/**
 * Trait AssertClientResponseTrait
 * @package Bytes\ResponseBundle\Test
 */
trait AssertClientResponseTrait
{
    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param string $message
     * @throws TransportExceptionInterface
     */
    public static function assertResponseIsSuccessful(ResponseInterface|ClientResponseInterface $response, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        self::assertThat(
            $response->getStatusCode(),
            self::logicalAnd(
                self::greaterThanOrEqual(200),
                self::lessThan(300)
            ),
            $message
        );
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param int $expectedCode
     * @param string $message
     */
    public static function assertResponseStatusCodeSame(ResponseInterface|ClientResponseInterface $response, int $expectedCode, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        self::assertThatForResponse($response, new ResponseStatusCodeSame($expectedCode), $message);
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param Constraint $constraint
     * @param string $message
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function assertThatForResponse(ResponseInterface|ClientResponseInterface $response, Constraint $constraint, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        try {
            self::assertThat($response, $constraint, $message);
        } catch (ExpectationFailedException $exception) {
            $headers = $response->getHeaders(false);
            if (array_key_exists('X-Debug-Exception', $headers) && array_key_exists('X-Debug-Exception-File', $headers)) {
                if (($serverExceptionMessage = $headers['X-Debug-Exception'][0])
                    && ($serverExceptionFile = $headers['X-Debug-Exception-File'][0])) {
                    $serverExceptionFile = explode(':', $serverExceptionFile);
                    $exception->__construct($exception->getMessage(), $exception->getComparisonFailure(), new ErrorException(rawurldecode($serverExceptionMessage), 0, 1, rawurldecode($serverExceptionFile[0]), $serverExceptionFile[1]), $exception->getPrevious());
                }
            }

            throw $exception;
        }
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param int $expectedCode
     * @param string $message
     */
    public static function assertResponseStatusCodeNotSame(ResponseInterface|ClientResponseInterface $response, int $expectedCode, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        self::assertThatForResponse($response, static::logicalNot(new ResponseStatusCodeSame($expectedCode)), $message);
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param string $headerName
     * @param string $message
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function assertResponseHasHeader(ResponseInterface|ClientResponseInterface $response, string $headerName, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        self::assertThatForResponse($response, new ResponseConstraint\ResponseHasHeader($headerName), $message);
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param string $message
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function assertResponseHasContent(ResponseInterface|ClientResponseInterface $response, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        static::assertThat($response->getContent(false), static::logicalNot(static::isEmpty()), $message);
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param string $message
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function assertResponseHasNoContent(ResponseInterface|ClientResponseInterface $response, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        static::assertThat($response->getContent(false), static::logicalAnd(static::isEmpty()), $message);
    }

    /**
     * @param ResponseInterface|ClientResponseInterface $response
     * @param string $content
     * @param string $message
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function assertResponseContentSame(ResponseInterface|ClientResponseInterface $response, string $content, string $message = ''): void
    {
        if ($response instanceof ClientResponseInterface) {
            $response = $response->getResponse();
        }
        self::assertThatForResponse($response, new ResponseContentSame($content), $message);
    }
}