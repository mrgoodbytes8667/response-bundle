<?php

namespace Bytes\ResponseBundle\Security\Traits;

use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

/**
 * Trait CreateAuthenticatedTokenTrait.
 */
trait CreateAuthenticatedTokenTrait
{
    /**
     * Creates a PostAuthenticationToken with the entity id of the token attached as attribute accessToken.
     *
     * @return PostAuthenticationToken
     */
    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        if (!$passport instanceof UserPassportInterface) {
            throw new LogicException(sprintf('Passport does not contain a user, overwrite "createAuthenticatedToken()" in "%s" to create a custom authenticated token.', static::class));
        }

        $token = new PostAuthenticationToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
        $token->setAttribute('accessToken', $passport->getAttribute('accessToken')?->getId());
        $token->setAttribute('tokenIdentifier', $passport->getAttribute('tokenIdentifier'));

        return $token;
    }
}
