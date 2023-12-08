<?php

namespace Bytes\ResponseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class HttpClientPass
 * Sets the serializer, validator, and dispatcher dependencies on every bytes_response.http_client
 * Also sets the annotation reader on api, and urlGenerator on token classes.
 */
class HttpClientPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        // find all service IDs with the bytes_response.http_client tag
        $taggedServices = $container->findTaggedServiceIds('bytes_response.http_client');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall('setSerializer', [new Reference('serializer')]);
            $definition->addMethodCall('setValidator', [new Reference('validator')]);
            $definition->addMethodCall('setReader', [new Reference('annotations.cached_reader')]);
        }

        // find all service IDs with the bytes_response.http_client.api tag
        $taggedServices = $container->findTaggedServiceIds('bytes_response.http_client.api');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall('setSecurity', [
                new Reference('security.helper', ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE),
            ]);
        }

        // find all service IDs with the bytes_response.http_client.token tag
        $taggedServices = $container->findTaggedServiceIds('bytes_response.http_client.token');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall('setUrlGenerator', [new Reference('router.default')]);
        }
    }
}
