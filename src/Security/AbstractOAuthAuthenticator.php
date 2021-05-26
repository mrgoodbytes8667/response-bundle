<?php


namespace Bytes\ResponseBundle\Security;


use Bytes\ResponseBundle\Handler\Locator;
use Bytes\ResponseBundle\HttpClient\Token\TokenClientInterface;
use Bytes\ResponseBundle\Security\Traits\AuthenticationSuccessTrait;
use Bytes\ResponseBundle\Security\Traits\CreateAuthenticatedTokenTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function Symfony\Component\String\u;

/**
 * Class AbstractOAuthAuthenticator
 * @package Bytes\ResponseBundle\Security
 */
abstract class AbstractOAuthAuthenticator implements AuthenticatorInterface
{
    use TargetPathTrait, AuthenticationSuccessTrait, CreateAuthenticatedTokenTrait;

    /**
     * Error message for a invalid user at login
     */
    const REDIRECT_TO_REGISTRATION = 'redirect_to_registration';

    /**
     * AbstractOAuthAuthenticator constructor.
     * @param EntityManagerInterface $em
     * @param ServiceEntityRepository $userRepository
     * @param Security $security
     * @param UrlGeneratorInterface $urlGenerator
     * @param Locator $httpClientOAuthLocator
     * @param TokenClientInterface $client
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param string $userIdField
     * @param string $loginRoute
     * @param string $loginSuccessRoute
     * @param string $loginFailureRoute
     * @param string $registrationRoute
     * @param string $redirectToRegistrationRoute
     */
    public function __construct(
        protected EntityManagerInterface $em, protected ServiceEntityRepository $userRepository, protected Security $security,
        protected UrlGeneratorInterface $urlGenerator, protected Locator $httpClientOAuthLocator,
        protected TokenClientInterface $client, protected CsrfTokenManagerInterface $csrfTokenManager,
        protected string $userIdField, protected string $loginRoute, protected string $loginSuccessRoute,
        protected string $loginFailureRoute, protected string $registrationRoute, protected string $redirectToRegistrationRoute)
    {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     * @return bool|null
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
     * @param Request $request
     * @return PassportInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function authenticate(Request $request): PassportInterface
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
            $user = $this->setUserDetails($user, $tokenResponse, $validationResponse);
        } else {
            $user = $this->getUser($tokenResponse, $validationResponse);
        }

        // check credentials - e.g. make sure the password is valid

        $passport = new SelfValidatingPassport(new UserBadge($user->getUsername()));
        $passport->setAttribute('accessToken', $tokenResponse);
        $passport->setAttribute('tokenIdentifier', $tokenResponse->getIdentifier());
        return $passport;
    }

    /**
     * @return string
     */
    abstract protected function getOAuthTag(): string;

    /**
     * For user registrations, validate the state against the passed user
     * @param string $requestState
     * @param UserInterface $user
     * @return bool
     */
    abstract protected function validateRegistrationState(string $requestState, UserInterface $user): bool;

    /**
     * @param AccessTokenInterface $tokenResponse
     * @return TokenValidationResponseInterface
     */
    protected function validateToken(AccessTokenInterface $tokenResponse): TokenValidationResponseInterface
    {
        $validate = $this->client->validateToken($tokenResponse);

        if (empty($validate)) {
            throw new AuthenticationException();
        }
        return $validate;
    }

    /**
     * Set any details on the user entity that are needed to tie the user to this authentication mechanism
     * @param UserInterface $user
     * @param AccessTokenInterface $tokenResponse
     * @param TokenValidationResponseInterface $validationResponse
     * @return UserInterface
     */
    abstract protected function setUserDetails(UserInterface $user, AccessTokenInterface $tokenResponse, TokenValidationResponseInterface $validationResponse): UserInterface;

    /**
     * The $credentials argument is the value returned by getCredentials().
     * Your job is to return an object that implements UserInterface. If you
     * do, then checkCredentials() will be called. If you return null (or
     * throw an AuthenticationException) authentication will fail.
     *
     * @param AccessTokenInterface $tokenResponse
     * @param TokenValidationResponseInterface $validationResponse
     *
     * @return UserInterface|null
     *
     * @throws AuthenticationException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws UserNotFoundException
     */
    protected function getUser(AccessTokenInterface $tokenResponse, TokenValidationResponseInterface $validationResponse)
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
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        if($exception::class === AuthenticationException::class && $exception->getMessage() === self::REDIRECT_TO_REGISTRATION)
        {
            $url = $this->redirectToRegistrationRoute;
        } else {
            $url = $this->loginFailureRoute;
        }

        return new RedirectResponse($this->urlGenerator->generate($url));
    }
}