<?php

namespace Bytes\ResponseBundle\Controller;

use Bytes\ResponseBundle\Routing\OAuthInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class OAuthController
 * A controller that can be repackaged for each OAuth class.
 */
class OAuthController
{
    /**
     * OAuthController constructor.
     */
    public function __construct(protected OAuthInterface $oauth, protected UrlGeneratorInterface $urlGenerator, protected string $destinationRoute)
    {
    }

    /**
     * Route("/redirect", name="responsebundle_oauth_redirect").
     */
    public function redirectAction(): RedirectResponse
    {
        return new RedirectResponse($this->oauth->getAuthorizationUrl(), Response::HTTP_FOUND);
    }

    /**
     * Route("/handler", name="responsebundle_oauth_handler").
     */
    public function handlerAction(): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate($this->destinationRoute));
    }
}
