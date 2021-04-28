<?php


namespace Bytes\ResponseBundle\DependencyInjection;


use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
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

        $twitch = self::processTwitchClientBundle($config);
        if(!empty($twitch)) {
            $container->prependExtensionConfig('bytes_twitch_client', $twitch);
        }
        $discord = self::processDiscordClientBundle($config);
        if(!empty($discord)) {
            //$container->prependExtensionConfig('bytes_discord', $discord);
        }
    }

    /**
     * @param array $values
     * @return array
     */
    private static function processDiscordClientBundle(array $values) {
        if(!isset($values['connections']['discord'])) {
            return [];
        }
        $config = $values['connections']['discord'];

        $userAgent = self::getUserAgent($values, 'discord');
        if(!empty($userAgent)) {
            $config['user_agent'] = $userAgent;
        }

        return $config;
    }

    /**
     * @param array $values
     * @return array
     */
    private static function processTwitchClientBundle(array $values) {
        if(!isset($values['connections']['twitch'])) {
            return [];
        }
        $config = $values['connections']['twitch'];

        $userAgent = self::getUserAgent($values, 'twitch');
        if(!empty($userAgent)) {
            $config['user_agent'] = $userAgent;
        }

        return $config;
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