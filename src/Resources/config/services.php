<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

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
};