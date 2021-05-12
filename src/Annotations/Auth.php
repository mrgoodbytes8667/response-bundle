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
    private $authRequired = false;

    /**
     * @var array
     */
    private $scopes = [];

    /**
     * Auth constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if(isset($values['value']))
        {
            $values['authRequired'] = $values['value'];
            unset($values['value']);
        }
        $this->set(...$values);
    }

    public function set(?string $identifier = null, TokenSource|string $tokenSource = null, bool $authRequired = false, ?array $scopes = [])
    {
        $this->setIdentifier($identifier);
        $this->setTokenSource($tokenSource);
        $this->setAuthRequired($authRequired);
        $this->setScopes($scopes);
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
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
     * @return TokenSource|null
     */
    public function getTokenSource(): ?TokenSource
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
    public function isAuthRequired(): bool
    {
        return $this->authRequired ?? false;
    }

    /**
     * @param bool $authRequired
     * @return $this
     */
    public function setAuthRequired(bool $authRequired): self
    {
        $this->authRequired = $authRequired;
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
}