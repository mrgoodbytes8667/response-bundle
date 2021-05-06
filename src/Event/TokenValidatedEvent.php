<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenValidatedEvent
 * Fired after a token is successfully validated.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class TokenValidatedEvent extends Event
{
    /**
     * TokenValidatedEvent constructor.
     * @param AccessTokenInterface $token
     * @param TokenValidationResponseInterface $validation
     */
    public function __construct(private AccessTokenInterface $token, private TokenValidationResponseInterface $validation)
    {
    }

    /**
     * @param AccessTokenInterface $token
     * @param TokenValidationResponseInterface $validation
     * @return static
     */
    #[Pure] public static function new(AccessTokenInterface $token, TokenValidationResponseInterface $validation): static
    {
        return new static($token, $validation);
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
     * @return TokenValidationResponseInterface
     */
    public function getValidation(): TokenValidationResponseInterface
    {
        return $this->validation;
    }

    /**
     * @param TokenValidationResponseInterface $validation
     * @return $this
     */
    public function setValidation(TokenValidationResponseInterface $validation): self
    {
        $this->validation = $validation;
        return $this;
    }
}
