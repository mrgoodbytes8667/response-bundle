<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RevokeTokenEvent
 * Fired to specify that a token needs to be revoked.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class RevokeTokenEvent extends Event
{
    /**
     * RevokeTokenEvent constructor.
     * @param AccessTokenInterface $token
     */
    public function __construct(private AccessTokenInterface $token)
    {
    }

    /**
     * @param AccessTokenInterface $token
     * @return static
     */
    #[Pure] public static function new(AccessTokenInterface $token): static
    {
        return new static($token);
    }

    /**
     * @return AccessTokenInterface
     */
    public function getToken(): AccessTokenInterface
    {
        return $this->token;
    }

    /**
     * @param AccessTokenInterface $token
     * @return $this
     */
    public function setToken(AccessTokenInterface $token): self
    {
        $this->token = $token;
        return $this;
    }
}
