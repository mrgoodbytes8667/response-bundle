<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ValidateTokenEvent
 * Fired to specify that a token needs to be validated.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class ValidateTokenEvent extends Event
{
    /**
     * @var TokenValidationResponseInterface|null
     */
    private $validation;

    /**
     * ValidateTokenEvent constructor.
     * @param AccessTokenInterface $token
     * @param UserInterface|null $user
     */
    public function __construct(private AccessTokenInterface $token, private ?UserInterface $user = null)
    {
        if(empty($user) && method_exists($token, 'getUser'))
        {
            $user = $token->getUser();
            $this->setUser($user);
        }
    }

    /**
     * @param AccessTokenInterface $token
     * @param UserInterface|null $user
     * @return static
     */
    public static function new(AccessTokenInterface $token, ?UserInterface $user = null): static
    {
        return new static($token, $user);
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
     * @return TokenValidationResponseInterface|null
     */
    public function getValidation(): ?TokenValidationResponseInterface
    {
        return $this->validation;
    }

    /**
     * @param TokenValidationResponseInterface|null $validation
     * @return $this
     */
    public function setValidation(?TokenValidationResponseInterface $validation): self
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