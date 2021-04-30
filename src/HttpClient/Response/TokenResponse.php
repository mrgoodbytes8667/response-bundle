<?php


namespace Bytes\ResponseBundle\HttpClient\Response;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use LogicException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TokenResponse
 * @package Bytes\ResponseBundle\HttpClient\Response
 */
class TokenResponse extends Response
{
    /**
     * Identifier used for differentiating different token providers
     * @return string|null
     */
    protected static function getIdentifier(): ?string {
        if(property_exists(static::class, 'identifer')) {
            return static::$identifer;
        }
        return null;
    }

    /**
     * @param bool $throw
     * @param array $context
     * @param string|null $type
     * @return array|AccessTokenInterface|mixed|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function deserialize(bool $throw = true, array $context = [], ?string $type = null)
    {
        $results = parent::deserialize($throw, $context, $type);
        if ($results instanceof AccessTokenInterface && method_exists($results, 'setClass') && !empty(static::getIdentifier())) {
            $results->setClass(static::getIdentifier());
        }
        if ($results instanceof AccessTokenInterface && method_exists($results, 'setTokenSource') && !empty(static::getTokenSource())) {
            $results->setTokenSource(static::getTokenSource());
        }
        return $results;
    }

    /**
     * Returns the TokenSource for the token
     * @return TokenSource
     *
     * @throws LogicException When this abstract method is not implemented
     */
    protected static function getTokenSource(): TokenSource
    {
        throw new LogicException('You must override the getTokenSource() method in the concrete response class.');
    }
}