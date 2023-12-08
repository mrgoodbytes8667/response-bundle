<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ObtainValidTokenEvent.
 */
class ObtainValidTokenEvent extends Event
{
    /**
     * ObtainValidTokenEvent constructor.
     */
    public function __construct(private string $identifier, private TokenSource $tokenSource, private ?UserInterface $user = null, private array $scopes = [], private ?AccessTokenInterface $token = null)
    {
        if ($tokenSource->equals(TokenSource::user, TokenSource::id) && empty($user)) {
            throw new InvalidArgumentException('Id and User tokens require a user.');
        }
    }

    public static function new(string $identifier, TokenSource $tokenSource, UserInterface $user = null, array $scopes = []): static
    {
        return new static($identifier, $tokenSource, $user, $scopes);
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

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes ?: [];
    }

    /**
     * @return $this
     */
    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function getToken(): ?AccessTokenInterface
    {
        return $this->token;
    }

    /**
     * @return $this
     */
    public function setToken(?AccessTokenInterface $token): self
    {
        $this->token = $token;

        return $this;
    }
}
