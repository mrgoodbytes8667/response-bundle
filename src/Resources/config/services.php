<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Bytes\ResponseBundle\Handler\HttpClientLocator;
use Bytes\ResponseBundle\Handler\Locator;
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
        ->public();

    $services->set('bytes_response.locator.http_client.api', HttpClientLocator::class)
        ->args([tagged_locator('bytes_response.http_client.api', 'key', 'getDefaultIndexName')])
        ->public();

    $services->set('bytes_response.locator.http_client.token', HttpClientLocator::class)
        ->args([tagged_locator('bytes_response.http_client.token', 'key', 'getDefaultIndexName')])
        ->public();

    $services->set('bytes_response.locator.oauth', Locator::class)
        ->args([tagged_locator('bytes_response.oauth', 'key', 'getDefaultIndexName')])
        ->public();

    $services->set('bytes_response.locator.authenticator.oauth', Locator::class)
        ->args([tagged_locator('bytes_response.authenticator.oauth', 'key', 'getDefaultIndexName')])
        ->public();

    $services->alias(Locator::class . ' $httpClientLocator', 'bytes_response.locator.http_client');
    $services->alias(Locator::class . ' $httpClientApiLocator', 'bytes_response.locator.http_client.api');
    $services->alias(Locator::class . ' $httpClientTokenLocator', 'bytes_response.locator.http_client.token');
    $services->alias(HttpClientLocator::class . ' $httpClientLocator', 'bytes_response.locator.http_client');
    $services->alias(HttpClientLocator::class . ' $httpClientApiLocator', 'bytes_response.locator.http_client.api');
    $services->alias(HttpClientLocator::class . ' $httpClientTokenLocator', 'bytes_response.locator.http_client.token');
    $services->alias(Locator::class . ' $httpClientOAuthLocator', 'bytes_response.locator.oauth');
    $services->alias(Locator::class . ' $authenticatorOAuthLocator', 'bytes_response.locator.authenticator.oauth');
    //endregion
};