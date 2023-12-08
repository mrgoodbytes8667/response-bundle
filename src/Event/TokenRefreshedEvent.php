<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenRefreshedEvent
 * Fired when a token has been refreshed. Any updates to the new and/or old token should be performed via this event.
 *
 * @experimental
 */
class TokenRefreshedEvent extends AbstractTokenEvent
{
    /**
     * TokenRefreshedEvent constructor.
     */
    public function __construct(AccessTokenInterface $token, private ?AccessTokenInterface $oldToken)
    {
        parent::__construct($token);
    }

    public static function new(AccessTokenInterface $token, AccessTokenInterface $oldToken = null): static
    {
        return new static($token, $oldToken);
    }

    public function getOldToken(): ?AccessTokenInterface
    {
        return $this->oldToken;
    }

    /**
     * @return $this
     */
    public function setOldToken(AccessTokenInterface $oldToken): self
    {
        $this->oldToken = $oldToken;

        return $this;
    }
}
