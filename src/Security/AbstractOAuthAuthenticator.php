<?php

namespace Bytes\ResponseBundle\Security;

use Bytes\ResponseBundle\Handler\Locator;
use Bytes\ResponseBundle\HttpClient\Token\TokenClientInterface;
use Bytes\ResponseBundle\Security\Traits\AuthenticationSuccessTrait;
use Bytes\ResponseBundle\Security\Traits\CreateAuthenticatedTokenTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

use function Symfony\Component\String\u;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractOAuthAuthenticator.
 */
abstract class AbstractOAuthAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    use TargetPathTrait;
    use AuthenticationSuccessTrait;
    use CreateAuthenticatedTokenTrait;

    /**
     * Error message for a invalid user at login.
     *
     * @var string
     */
    public const REDIRECT_TO_REGISTRATION = 'redirect_to_registration';

    /**
     * Error message for a duplicate user.
     *
     * @var string
     */
    public const REDIRECT_TO_LOGOUT = 'You are already registered. Please login.';

    /**
     * AbstractOAuthAuthenticator constructor.
     */
    public function __construct(
        protected EntityManagerInterface $em, protected ServiceEntityRepository $userRepository, protected Security $security,
        protected UrlGeneratorInterface $urlGenerator, protected Locator $httpClientOAuthLocator,
        protected TokenClientInterface $client, protected CsrfTokenManagerInterface $csrfTokenManager, protected TokenStorageInterface $tokenStorage,
        protected string $userIdField, protected string $loginRoute, protected string $loginSuccessRoute,
        protected string $loginFailureRoute, protected string $registrationRoute, protected string $redirectToRegistrationRoute)
    {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        if (!in_array($request->attributes->get('_route'), [$this->registrationRoute, $this->loginRoute])) {
            return false;
        }

        if (!$request->query->has('code') || !$request->query->has('state')) {
            return false;
        }

        if ($request->attributes->get('_route') == $this->loginRoute && !$this->security->getUser()) {
            return true;
        }

        if ($request->attributes->get('_route') == $this->registrationRoute) {
            return true;
        }

        return false;
    }

    /**
     * The $credentials argument is the value returned by getCredentials().
     * Your job is to return an object that implements UserInterface. If you
     * do, then checkCredentials() will be called. If you return null (or
     * throw an AuthenticationException) authentication will fail.
     *
     * @throws AuthenticationException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws UserNotFoundException
     */
    protected function getUser(AccessTokenInterface $tokenResponse, TokenValidationResponseInterface $validationResponse): ?UserInterface
    {
        $user = $this->userRepository->findOneBy([$this->userIdField => $validationResponse->getUserId()]);

        if (empty($user)) {
            // Technically this should be a UserNotFoundException, but that gets sanitized out. We know the user is
            // a valid oauth token at this point, so redirect them back to the registration page.
            throw new AuthenticationException(self::REDIRECT_TO_REGISTRATION);
        }

        return $user;
    }

    /**
     * Create a passport for the current request.
     *
     * The passport contains the user, credentials and any additional information
     * that has to be checked by the Symfony Security system. For example, a login
     * form authenticator will probably return a passport containing the user, the
     * presented password and the CSRF token value.
     *
     * You may throw any AuthenticationException in this method in case of error (e.g.
     * a UserNotFoundException when the user cannot be found).
     *
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function authenticate(Request $request): Passport
    {
        $oauthTag = u($this->getOAuthTag());
        $incomingState = u($request->query->get('state'));
        $code = $request->query->get('code');
        $state = $incomingState->slice(length: 26)->toString();

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($state, $incomingState->slice(start: 26)->toString()))) {
            throw new InvalidCsrfTokenException();
        }

        if ($request->attributes->get('_route') == $this->registrationRoute) {
            $user = $this->security->getUser();
            if (!$this->validateRegistrationState($state, $user)) {
                throw new InvalidCsrfTokenException();
            }

            $oauthTag = $oauthTag->append('-USER');
        } else {
            $oauthTag = $oauthTag->append('-LOGIN');
        }

        $this->client->setOAuth($this->httpClientOAuthLocator->get($oauthTag->toString()));

        $tokenResponse = $this->client->exchange($code);

        if (empty($tokenResponse)) {
            throw new AuthenticationException();
        }

        $validationResponse = $this->validateToken($tokenResponse);

        if ($request->attributes->get('_route') == $this->registrationRoute) {
            try {
                $this->handleDuplicateAccounts($user, $validationResponse);
                $user = $this->setUserDetails($user, $tokenResponse, $validationResponse);
            } catch (UniqueConstraintViolationException $exception) {
                throw new DuplicateAccountException(self::REDIRECT_TO_LOGOUT, previous: $exception);
            }
        } else {
            $user = $this->getUser($tokenResponse, $validationResponse);
        }

        // check credentials - e.g. make sure the password is valid

        $passport = new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        $passport->setAttribute('accessToken', $tokenResponse);
        $passport->setAttribute('tokenIdentifier', $tokenResponse->getIdentifier());

        return $passport;
    }

    protected function getOAuthTag(): string
    {
        return static::$tag;
    }

    /**
     * For user registrations, validate the state against the passed user.
     */
    abstract protected function validateRegistrationState(string $requestState, UserInterface $user): bool;

    protected function validateToken(AccessTokenInterface $tokenResponse): TokenValidationResponseInterface
    {
        $validate = $this->client->validateToken($tokenResponse);

        if (empty($validate)) {
            throw new AuthenticationException();
        }

        return $validate;
    }

    /**
     * Is this account already tied to another user?
     *
     * @throws DuplicateAccountException
     */
    protected function handleDuplicateAccounts(UserInterface $user, TokenValidationResponseInterface $validationResponse): UserInterface
    {
        $found = $this->userRepository->findBy([$this->userIdField => $validationResponse->getUserId()]);
        switch (count($found)) {
            case 0:
            case 1:
                return $user;
                break;
            default:
                $this->setDuplicateDetails($user);
                throw new DuplicateAccountException();
                break;
        }
    }

    abstract protected function setDuplicateDetails(UserInterface $user): UserInterface;

    /**
     * Set any details on the user entity that are needed to tie the user to this authentication mechanism.
     */
    abstract protected function setUserDetails(UserInterface $user, AccessTokenInterface $tokenResponse, TokenValidationResponseInterface $validationResponse): UserInterface;

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        }

        if (AuthenticationException::class === $exception::class && self::REDIRECT_TO_REGISTRATION === $exception->getMessage()) {
            $url = $this->redirectToRegistrationRoute;
        } elseif (DuplicateAccountException::class === $exception::class || (AuthenticationException::class === $exception::class && self::REDIRECT_TO_LOGOUT === $exception->getMessage())) {
            $this->tokenStorage->setToken(null);
            $url = $this->loginFailureRoute;
        } else {
            $url = $this->loginFailureRoute;
        }

        return new RedirectResponse($this->urlGenerator->generate($url));
    }
}
