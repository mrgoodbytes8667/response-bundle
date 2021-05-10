<?php


namespace Bytes\ResponseBundle\Security;


use Bytes\ResponseBundle\HttpClient\Token\TokenClientInterface;
use Bytes\ResponseBundle\Routing\OAuthInterface;
use Bytes\ResponseBundle\Security\Traits\AuthenticationSuccessTrait;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractOAuthAuthenticator
 * @package Bytes\ResponseBundle\Security
 */
abstract class AbstractOAuthAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait, AuthenticationSuccessTrait;

    /**
     * AbstractOAuthAuthenticator constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     * @param UrlGeneratorInterface $urlGenerator
     * @param OAuthInterface $oAuth
     * @param TokenClientInterface $client
     * @param string $loginRoute
     * @param string $loginSuccessRoute
     */
    public function __construct(protected EntityManagerInterface $em, protected Security $security, protected UrlGeneratorInterface $urlGenerator, protected OAuthInterface $oAuth, protected TokenClientInterface $client, protected string $loginRoute, protected string $loginSuccessRoute)
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
        // if there is already an authenticated user (likely due to the session)
        // then return false and skip authentication: there is no need.
        if ($this->security->getUser()) {
            return false;
        }
        // the user is not logged in, so the authenticator should continue

        if ($request->attributes->get('_route') == $this->loginRoute) {
            if (!$request->query->has('code') || !$request->query->has('state')) {
                return false;
            }
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
     * a UsernameNotFoundException when the user cannot be found).
     *
     * @param Request $request
     * @return PassportInterface
     * @throws AuthenticationException
     */
    public function authenticate(Request $request): PassportInterface
    {
        $code = $request->query->get('code');
        $state = $request->query->get('state');

        $tokenResponse = $this->client->exchange($code);

        $user = $this->getUser($tokenResponse);

        // check credentials - e.g. make sure the password is valid

//        $this->tokenService->createToken(['credentials' => $tokenResponse, 'service' => $this->getService(), 'user' => $user]);
//        $this->em->flush();

        return new SelfValidatingPassport($user);
    }

    /**
     * The $credentials argument is the value returned by getCredentials().
     * Your job is to return an object that implements UserInterface. If you
     * do, then checkCredentials() will be called. If you return null (or
     * throw an AuthenticationException) authentication will fail.
     *
     * @param AccessTokenInterface $tokenResponse
     *
     * @return UserInterface|null
     *
     * @throws ClientExceptionInterface
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    abstract protected function getUser(AccessTokenInterface $tokenResponse);

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
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}