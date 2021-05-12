<?php


namespace Bytes\ResponseBundle\Annotations;


use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Class Client
 * @package Bytes\ResponseBundle\Annotations
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Client
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
     * Client constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        if (isset($values['value'])) {
            $values['identifier'] = $values['value'];
            unset($values['value']);
        }
        $this->set(...$values);
    }

    /**
     * @param string|null $identifier
     * @param TokenSource|string|null $tokenSource
     * @return $this
     */
    public function set(?string $identifier = null, TokenSource|string|null $tokenSource = null): self
    {
        $this->setIdentifier($identifier);
        $this->setTokenSource($tokenSource);
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
        if(!is_null($tokenSource) && !($tokenSource instanceof TokenSource))
        {
            $tokenSource = TokenSource::from($tokenSource);
        }
        $this->tokenSource = $tokenSource;
        return $this;
    }
}