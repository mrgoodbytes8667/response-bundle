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
     * @param UserInterface|null $user
     */
    public function __construct(private AccessTokenInterface $token, private TokenValidationResponseInterface $validation, private ?UserInterface $user = null)
    {
        if(empty($user) && method_exists($token, 'getUser'))
        {
            $user = $token->getUser();
            $this->setUser($user);
        }
    }

    /**
     * @param AccessTokenInterface $token
     * @param TokenValidationResponseInterface $validation
     * @param UserInterface|null $user
     * @return static
     */
    #[Pure] public static function new(AccessTokenInterface $token, TokenValidationResponseInterface $validation, ?UserInterface $user = null): static
    {
        return new static($token, $validation, $user);
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
}
