<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenValidatedEvent
 * Fired after a token is successfully validated.
 *
 * @experimental
 */
class TokenValidatedEvent extends Event
{
    /**
     * TokenValidatedEvent constructor.
     */
    public function __construct(private AccessTokenInterface $token, private TokenValidationResponseInterface $validation, private ?UserInterface $user = null)
    {
        if (empty($user) && method_exists($token, 'getUser')) {
            $user = $token->getUser();
            $this->setUser($user);
        }
    }

    #[Pure]
    public static function new(AccessTokenInterface $token, TokenValidationResponseInterface $validation, UserInterface $user = null): static
    {
        return new static($token, $validation, $user);
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

    public function getValidation(): TokenValidationResponseInterface
    {
        return $this->validation;
    }

    /**
     * @return $this
     */
    public function setValidation(TokenValidationResponseInterface $validation): self
    {
        $this->validation = $validation;

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
}
