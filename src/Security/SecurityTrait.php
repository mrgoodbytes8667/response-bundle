<?php

namespace Bytes\ResponseBundle\Security;

use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Trait SecurityTrait.
 */
trait SecurityTrait
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @var UserInterface|null
     */
    protected $tokenUser;

    /**
     * @return $this
     */
    public function setSecurity(?Security $security): self
    {
        $this->security = $security;

        return $this;
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @throws LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getTokenUser(): ?UserInterface
    {
        if (!empty($this->tokenUser)) {
            return $this->tokenUser;
        }

        if (empty($this->security)) {
            return null;
        }

        if (null === $token = $this->security->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    /**
     * @return $this
     */
    public function setTokenUser(?UserInterface $tokenUser): self
    {
        $this->tokenUser = $tokenUser;

        return $this;
    }
}
