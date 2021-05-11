<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Bytes\ResponseBundle\Handler\HttpClientLocator;
use Bytes\ResponseBundle\Handler\OAuthLocator;
use Bytes\ResponseBundle\HttpClient\Response\Response;

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

    //region Locators
    $services->set('bytes_response.locator.http_client', HttpClientLocator::class)
        ->args([tagged_locator('bytes_response.http_client', 'key', 'getDefaultIndexName')])
        ->alias(HttpClientLocator::class, 'bytes_response.locator.http_client')
        ->public();

    $services->set('bytes_response.locator.oauth', OAuthLocator::class)
        ->args([tagged_locator('bytes_response.oauth', 'key', 'getDefaultIndexName')])
        ->alias(OAuthLocator::class, 'bytes_response.locator.oauth')
        ->public();
    //endregion
};