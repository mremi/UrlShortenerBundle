<?php

namespace Mremi\UrlShortenerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MremiUrlShortenerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('chain.xml');
        $loader->load('http.xml');

        $this->configureBitly($container, $config, $loader);
        $this->configureGoogle($container, $config, $loader);
    }

    /**
     * Configures the Bit.ly services
     *
     * @param ContainerBuilder $container A container builder instance
     * @param array            $config    An array of configuration
     * @param XmlFileLoader    $loader    An XML file loader instance
     */
    private function configureBitly(ContainerBuilder $container, array $config, XmlFileLoader $loader)
    {
        if (false === $config['bitly']['enabled']) {
            return;
        }

        $loader->load('bitly.xml');

        $definition = $container->getDefinition('mremi_url_shortener.bitly.oauth_client');
        $definition->replaceArgument(1, $config['bitly']['username']);
        $definition->replaceArgument(2, $config['bitly']['password']);
    }

    /**
     * Configures the Google service
     *
     * @param ContainerBuilder $container A container builder instance
     * @param array            $config    An array of configuration
     * @param XmlFileLoader    $loader    An XML file loader instance
     */
    private function configureGoogle(ContainerBuilder $container, array $config, XmlFileLoader $loader)
    {
        if (false === $config['google']['enabled']) {
            return;
        }

        $loader->load('google.xml');

        $definition = $container->getDefinition('mremi_url_shortener.google.provider');
        $definition->replaceArgument(1, $config['google']['api_key']);
    }
}
