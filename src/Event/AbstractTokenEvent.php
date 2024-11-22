<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractTokenEvent extends Event
{
    public function __construct(private AccessTokenInterface $token)
    {
    }

    public static function new(AccessTokenInterface $token): static
    {
        return new static($token);
    }

    public function getToken(): AccessTokenInterface
    {
        return $this->token;
    }

    /**
     * @return $this
     */
    public function setToken(AccessTokenInterface $token): self
    {
        $this->token = $token;

        return $this;
    }
}
