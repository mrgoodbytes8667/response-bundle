<?php


namespace Bytes\ResponseBundle\DependencyInjection;


use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Class BytesResponseExtension
 * @package Bytes\ResponseBundle\DependencyInjection
 */
class BytesResponseExtension extends Extension implements ExtensionInterface, PrependExtensionInterface
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {
        // process the configuration of this extension
        $configs = $container->getExtensionConfig($this->getAlias());

        // resolve config parameters e.g. %kernel.debug% to its boolean value
        $resolvingBag = $container->getParameterBag();
        $configs = $resolvingBag->resolveValue($configs);

        // use the Configuration class to generate a config array that will be applied to bytes_twitch_response
        /** @var array $config = ['twitch' => ['client_id' => '', 'client_secret' => '', 'hub_secret' => '', 'user_agent' => '', 'eventsub_subscribe_callback_route_name' => '']] */
        $config = $this->processConfiguration(new Configuration(), $configs);

//        if (isset($config['twitch']) && isset($config['twitch']['hub_secret'])) {
//            $config['twitch'] = ['hub_secret' => $config['twitch']['hub_secret']];
//            $container->prependExtensionConfig('bytes_twitch_response', $config);
//        }
    }
}