<?php

namespace Bytes\ResponseBundle\HttpClient\Response;

use Bytes\ResponseBundle\Exception\Response\EmptyContentException;
use Bytes\ResponseBundle\HttpClient\ClientTrait;
use Bytes\ResponseBundle\Interfaces\ClientTokenResponseInterface;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TokenResponse.
 */
class TokenResponse extends Response implements ClientTokenResponseInterface
{
    use ClientTrait;

    /**
     * TokenResponse constructor.
     */
    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $dispatcher = null, bool $throwOnDeserializationWhenContentEmpty = true)
    {
        parent::__construct($serializer, $dispatcher, $throwOnDeserializationWhenContentEmpty);
    }

    /**
     * @return array|AccessTokenInterface|mixed|null
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws EmptyContentException
     */
    public function deserialize(bool $throw = true, array $context = [], string $type = null)
    {
        $this->prependOnDeserializeCallable(function ($self, $results) {
            if ($results instanceof AccessTokenInterface && method_exists($results, 'setIdentifier') && !empty($this->getIdentifier())) {
                $results->setIdentifier($this->getIdentifier());
            }

            if ($results instanceof AccessTokenInterface && method_exists($results, 'setTokenSource') && !empty($this->getTokenSource())) {
                $results->setTokenSource($this->getTokenSource());
            }

            return $results;
        });

        return parent::deserialize($throw, $context, $type);
    }
}
