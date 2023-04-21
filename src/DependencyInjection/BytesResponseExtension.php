<?php


namespace Bytes\ResponseBundle\DependencyInjection;


use Bytes\ResponseBundle\Objects\ConfigNormalizer;
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

        // use the Configuration class to generate a config array that will be applied to the child bundles
        $config = $this->processConfiguration(new Configuration(), $configs);

        $bundles = $container->getParameter('kernel.bundles');
        // determine if BytesDiscordBundle or BytesTwitchClientBundle is registered
        if (isset($bundles['BytesDiscordBundle']) || isset($bundles['BytesTwitchClientBundle'])) {
            // Normalize, process, and prepend extension config if found
            foreach ($container->getExtensions() as $name => $extension) {
                if($extension instanceof ResponseExtensionInterface) {
                    switch ($name) {
                        case 'bytes_discord_client':
                        case 'bytes_twitch_client':
                            $clientConfig = self::processResponseClientBundle($name === 'bytes_discord_client' ? 'discord' : 'twitch', $config, $extension);
                            if (!empty($clientConfig)) {
                                $container->prependExtensionConfig($name, $clientConfig);
                            }
                            
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param string $key
     * @param array $values
     * @param ResponseExtensionInterface $extension
     * @return array
     */
    private static function processResponseClientBundle(string $key, array $values, ResponseExtensionInterface $extension) {
        if(!isset($values['connections'][$key])) {
            return [];
        }
        
        $config = $values['connections'][$key];

        $userAgent = self::getUserAgent($values, $key);
        if(!empty($userAgent)) {
            $config['user_agent'] = $userAgent;
        }

        return ConfigNormalizer::normalizeEndpoints($config, $extension::getEndpoints(), $extension::getAddRemoveParents());
    }

    /**
     * @param array $config
     * @param string $section = ['twitch']['discord'][$any]
     * @return string
     */
    private static function getUserAgent(array $config, string $section)
    {
        if(isset($config['connections'][$section]['user_agent'])) {
            return $config['connections'][$section]['user_agent'];
        }
        
        if(isset($config['defaults']['user_agent'])) {
            return $config['defaults']['user_agent'];
        }
        
        return '';
    }
}