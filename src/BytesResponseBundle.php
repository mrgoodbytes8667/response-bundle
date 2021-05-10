<?php


namespace Bytes\ResponseBundle;


use Bytes\ResponseBundle\DependencyInjection\Compiler\OAuthHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class BytesResponseBundle
 * @package Bytes\ResponseBundle
 */
class BytesResponseBundle extends Bundle
{
    /**
     * Use this method to register compiler passes and manipulate the container during the building process.
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new OAuthHandlerPass());
    }
}