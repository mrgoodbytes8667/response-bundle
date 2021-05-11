<?php


namespace Bytes\ResponseBundle\Security;


use LogicException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Trait SecurityTrait
 * @package Bytes\ResponseBundle\Security
 */
trait SecurityTrait
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @param Security|null $security
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
     * @return UserInterface|null
     *
     * @throws LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser(): ?UserInterface
    {
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
}