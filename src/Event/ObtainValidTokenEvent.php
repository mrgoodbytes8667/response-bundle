<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ObtainValidTokenEvent
 * @package Bytes\ResponseBundle\Event
 */
class ObtainValidTokenEvent extends Event
{
    /**
     * ObtainValidTokenEvent constructor.
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @param UserInterface|null $user
     * @param AccessTokenInterface|null $token
     */
    public function __construct(private string $identifier, private TokenSource $tokenSource, private ?UserInterface $user = null, private array $scopes = [], private ?AccessTokenInterface $token = null)
    {
        if ($tokenSource->equals(TokenSource::user(), TokenSource::id()) && empty($user)) {
            throw new InvalidArgumentException('Id and User tokens require a user.');
        }
    }

    /**
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @param UserInterface|null $user
     * @param array $scopes
     * @return static
     */
    public static function new(string $identifier, TokenSource $tokenSource, ?UserInterface $user = null, array $scopes = []): static
    {
        return new static($identifier, $tokenSource, $user, $scopes);
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

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     * @return $this
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes ?: [];
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;
        return $this;
    }

    /**
     * @return AccessTokenInterface|null
     */
    public function getToken(): ?AccessTokenInterface
    {
        return $this->token;
    }

    /**
     * @param AccessTokenInterface|null $token
     * @return $this
     */
    public function setToken(?AccessTokenInterface $token): self
    {
        $this->token = $token;
        return $this;
    }
}