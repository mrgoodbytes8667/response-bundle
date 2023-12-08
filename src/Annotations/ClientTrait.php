<?php

namespace Bytes\ResponseBundle\Annotations;

use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Trait ClientTrait.
 */
trait ClientTrait
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var TokenSource
     */
    private $tokenSource;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return $this
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getTokenSource(): ?TokenSource
    {
        return $this->tokenSource;
    }

    /**
     * @return $this
     */
    public function setTokenSource(TokenSource|string|null $tokenSource): self
    {
        if (!is_null($tokenSource) && !($tokenSource instanceof TokenSource)) {
            $tokenSource = TokenSource::from($tokenSource);
        }

        $this->tokenSource = $tokenSource;

        return $this;
    }
}
