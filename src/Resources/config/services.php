<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Bytes\ResponseBundle\Controller\OAuthController;
use Bytes\ResponseBundle\HttpClient\Response\Response;
use Bytes\ResponseBundle\Security\OAuthHandlerCollection;

/**
 * @param ContainerConfigurator $container
 */
return static function (ContainerConfigurator $container) {

    $services = $container->services();

    //region Response
    $services->set('bytes_response.httpclient.response', Response::class)
        ->args([
            service('serializer'), // Symfony\Component\Serializer\SerializerInterface
        ])
        ->alias(Response::class, 'bytes_response.httpclient.response')
        ->public();
    //endregion

    //region Handlers
    $services->set('bytes_response.security.oauth.handler', OAuthHandlerCollection::class)
        ->args([tagged_locator('bytes_response.security.oauth', 'key', 'getDefaultIndexName')])
        ->alias(OAuthHandlerCollection::class, 'bytes_response.security.oauth.handler')
        ->public();
    //endregion
};