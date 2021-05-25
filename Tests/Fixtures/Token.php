<?php


namespace Bytes\ResponseBundle\Tests\Fixtures;


use Bytes\ResponseBundle\Token\AccessTokenCreateUpdateFromTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Token
 * @package Bytes\ResponseBundle\Tests\Fixtures
 */
class Token implements AccessTokenInterface
{
    use AccessTokenCreateUpdateFromTrait;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * @param ...$args
     * @return static
     */
    public static function createFromParts(...$args): static
    {
        return new static();
    }

    /**
     * Update the current access token with details from another access token (ie: a refresh token)
     * @param AccessTokenInterface $token
     * @return $this
     * @throws Exception
     */
    public function updateFromAccessToken(AccessTokenInterface $token)
    {
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