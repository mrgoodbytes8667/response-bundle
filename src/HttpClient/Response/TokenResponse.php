<?php


namespace Bytes\ResponseBundle\HttpClient\Response;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Exception\Response\EmptyContentException;
use Bytes\ResponseBundle\HttpClient\TokenSourceIdentifierTrait;
use Bytes\ResponseBundle\Interfaces\ClientTokenResponseInterface;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TokenResponse
 * @package Bytes\ResponseBundle\HttpClient\Response
 */
class TokenResponse extends Response implements ClientTokenResponseInterface
{
    use TokenSourceIdentifierTrait;

    /**
     * TokenResponse constructor.
     * @param SerializerInterface $serializer
     * @param EventDispatcherInterface|null $dispatcher
     * @param bool $throwOnDeserializationWhenContentEmpty
     */
    public function __construct(SerializerInterface $serializer, ?EventDispatcherInterface $dispatcher = null, bool $throwOnDeserializationWhenContentEmpty = true)
    {
        parent::__construct($serializer, $dispatcher, $throwOnDeserializationWhenContentEmpty);
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
     * @throws InvalidArgumentException
     * @throws EmptyContentException
     */
    public function deserialize(bool $throw = true, array $context = [], ?string $type = null)
    {
        $this->prependOnDeserializeCallable(function ($self, $results) {
            if ($results instanceof AccessTokenInterface && method_exists($results, 'setIdentifier') && !empty(static::getIdentifier())) {
                $results->setIdentifier(static::getIdentifier());
            }
            if ($results instanceof AccessTokenInterface && method_exists($results, 'setTokenSource') && !empty(static::getTokenSource())) {
                $results->setTokenSource(static::getTokenSource());
            }
            return $results;
        });
        return parent::deserialize($throw, $context, $type);
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