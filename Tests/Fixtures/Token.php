<?php

namespace Bytes\ResponseBundle\Tests\Fixtures;

use Bytes\ResponseBundle\Token\AccessTokenCreateUpdateFromTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Token.
 */
class Token implements AccessTokenInterface
{
    use AccessTokenCreateUpdateFromTrait;

    private ?UserInterface $user = null;

    public static function createFromParts(...$args): static
    {
        return new static();
    }

    /**
     * Update the current access token with details from another access token (ie: a refresh token).
     *
     * @return $this
     *
     * @throws Exception
     */
    public function updateFromAccessToken(AccessTokenInterface $token)
    {
        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }
}
