<?php


namespace Bytes\ResponseBundle\Annotations;


use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Class Auth
 * @package Bytes\ResponseBundle\Annotations
 *
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Auth
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var TokenSource
     */
    private $tokenSource;

    /**
     * @var bool
     */
    private $auth;

    /**
     * @var bool
     */
    private $user;

    /**
     * @var array
     */
    private $scopes;

    /**
     * Auth constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
    }

    /**
     * @return string
     */
    public function getIdentifier(): mixed
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier(?string $identifier): self
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
     * @param TokenSource|string|null $tokenSource
     * @return $this
     */
    public function setTokenSource(TokenSource|string|null $tokenSource): self
    {
        if(!empty($tokenSource) && is_string($tokenSource))
        {
            if(TokenSource::isValid($tokenSource))
            {
                $tokenSource = TokenSource::from($tokenSource);
            }
        }
        $this->tokenSource = $tokenSource;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAuth(): bool
    {
        return $this->auth;
    }

    /**
     * @param bool $auth
     * @return $this
     */
    public function setAuth(bool $auth): self
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->user;
    }

    /**
     * @param bool $user
     * @return $this
     */
    public function setUser(bool $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
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
}