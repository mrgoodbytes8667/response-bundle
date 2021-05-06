<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenRefreshedEvent
 * Fired when a token has been refreshed. Any updates to the new and/or old token should be performed via this event.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class TokenRefreshedEvent extends AbstractTokenEvent
{
    /**
     * TokenRefreshedEvent constructor.
     * @param AccessTokenInterface $token
     * @param AccessTokenInterface $oldToken
     */
    public function __construct(AccessTokenInterface $token, private AccessTokenInterface $oldToken)
    {
        parent::__construct($token);
    }

    /**
     * @param AccessTokenInterface $token
     * @param AccessTokenInterface|null $oldToken
     * @return static
     */
    public static function new(AccessTokenInterface $token, AccessTokenInterface $oldToken = null): static
    {
        return new static($token, $oldToken);
    }

    /**
     * @return AccessTokenInterface|null
     */
    public function getOldToken(): ?AccessTokenInterface
    {
        return $this->oldToken;
    }

    /**
     * @param AccessTokenInterface $oldToken
     * @return $this
     */
    public function setOldToken(AccessTokenInterface $oldToken): self
    {
        $this->oldToken = $oldToken;
        return $this;
    }
}