<?php


namespace Bytes\ResponseBundle\DependencyInjection\Compiler;


use Bytes\ResponseBundle\Security\OAuthHandlerCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ReflectionException;

class OAuthHandlerPass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     * @param ContainerBuilder $container
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        $commandServices = $container->findTaggedServiceIds('bytes_response.security.oauth', true);
        $lazyCommandMap = [];

        foreach ($commandServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());

            if (isset($tags[0]['key'])) {
                $commandName = $tags[0]['key'];
            } else {
                if (!$r = $container->getReflectionClass($class)) {
                    throw new \InvalidArgumentException(sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
                }
                $commandName = $class::getDefaultIndexName();
            }

            unset($tags[0]);
            $lazyCommandMap[$commandName] = $id;

            foreach ($tags as $tag) {
                if (isset($tag['key'])) {
                    $lazyCommandMap[$tag['key']] = $id;
                }
            }
        }

        $container->register('bytes_response.security.oauth.handler', OAuthHandlerCollection::class)
            ->setArguments([$lazyCommandMap]);
    }
}