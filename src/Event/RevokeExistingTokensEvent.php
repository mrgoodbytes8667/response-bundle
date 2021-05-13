<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RevokeExistingTokensEvent
 * Fired to specify that all existing tokens aside from $token should be revoked.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class RevokeExistingTokensEvent extends Event
{
    /**
     * RevokeExistingTokensEvent constructor.
     * @param AccessTokenInterface $token
     * @param UserInterface $user
     * @param string $identifier
     * @param TokenSource $tokenSource
     */
    public function __construct(private AccessTokenInterface $token, private UserInterface $user, private string $identifier, private TokenSource $tokenSource)
    {
    }

    /**
     * @param AccessTokenInterface $token
     * @param UserInterface $user
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @return static
     */
    public static function new(AccessTokenInterface $token, UserInterface $user, string $identifier, TokenSource $tokenSource): static
    {
        return new static($token, $user, $identifier, $tokenSource);
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

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return TokenSource
     */
    public function getTokenSource(): TokenSource
    {
        return $this->tokenSource;
    }

    /**
     * @param TokenSource $tokenSource
     * @return $this
     */
    public function setTokenSource(TokenSource $tokenSource): self
    {
        $this->tokenSource = $tokenSource;
        return $this;
    }
}
