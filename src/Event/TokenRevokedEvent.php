<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenRevokedEvent
 * Fired after a token is successfully revoked.
 * @package App\Event
 *
 * @experimental
 */
class TokenRevokedEvent extends Event
{
    /**
     * @Event("Bytes\ResponseBundle\Event\TokenRevokedEvent")
     */
    public const NAME = 'bytes_response.token.revoked';

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
