<?php

namespace Bytes\ResponseBundle\Tests\Controller;

use Bytes\ResponseBundle\Controller\OAuthController;
use Bytes\ResponseBundle\Routing\OAuthInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OAuthControllerTest extends TestCase
{
    public function testHandlerAction()
    {
        $authorizationUrl = 'client-id';

        $oauth = self::createMock(OAuthInterface::class);
        $oauth->method('getAuthorizationUrl')
            ->willReturn($authorizationUrl);

        $url = self::createMock(UrlGeneratorInterface::class);

        $destinationRoute = 'destination-route';

        $controller = new OAuthController(oauth: $oauth, urlGenerator: $url, destinationRoute: $destinationRoute);

        $response = $controller->redirectAction();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame($authorizationUrl, $response->getTargetUrl());
    }

    public function testRedirectAction()
    {
        $destinationRoute = 'destination-route';
        $destinationUrl = 'http://destination-route.example';

        $oauth = self::createMock(OAuthInterface::class);

        $url = self::createMock(UrlGeneratorInterface::class);
        $url->method('generate')
            ->willReturn($destinationUrl);

        $controller = new OAuthController(oauth: $oauth, urlGenerator: $url, destinationRoute: $destinationRoute);

        $response = $controller->handlerAction();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame($destinationUrl, $response->getTargetUrl());
    }
}
