<?php

namespace Bytes\ResponseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class OAuthPass
 * Sets the url generator, validator, and security, and csrf token manager dependencies on every service that is
 * tagged "bytes_response.oauth".
 */
class OAuthPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        // find all service IDs with the bytes_response.oauth tag
        $taggedServices = $container->findTaggedServiceIds('bytes_response.oauth');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall('setUrlGenerator', [new Reference('router.default')]); // Symfony\Component\Routing\Generator\UrlGeneratorInterface
            $definition->addMethodCall('setValidator', [new Reference('validator')]);
            $definition->addMethodCall('setSecurity', [new Reference('security.helper')]); // Symfony\Component\Security\Core\Security
            $definition->addMethodCall('setCsrfTokenManager', [new Reference('security.csrf.token_manager')]); // Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
        }
    }
}
