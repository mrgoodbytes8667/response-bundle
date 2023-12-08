<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RevokeExistingTokensEvent
 * Fired to specify that all existing tokens aside from $token should be revoked.
 *
 * @experimental
 */
class RevokeExistingTokensEvent extends Event
{
    /**
     * RevokeExistingTokensEvent constructor.
     */
    public function __construct(private AccessTokenInterface $token, private UserInterface $user, private string $identifier, private TokenSource $tokenSource)
    {
    }

    public static function new(AccessTokenInterface $token, UserInterface $user, string $identifier, TokenSource $tokenSource): static
    {
        return new static($token, $user, $identifier, $tokenSource);
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

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return $this
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getTokenSource(): TokenSource
    {
        return $this->tokenSource;
    }

    /**
     * @return $this
     */
    public function setTokenSource(TokenSource $tokenSource): self
    {
        $this->tokenSource = $tokenSource;

        return $this;
    }
}
