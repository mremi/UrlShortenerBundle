<?php

namespace Mremi\UrlShortenerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('bitly.xml');

        $this->configureBitly($container, $config);
    }

    /**
     * Configures the Bit.ly services
     *
     * @param ContainerBuilder $container A container builder instance
     * @param array            $config    An array of configuration
     */
    private function configureBitly(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition('mremi_url_shortener.bitly.oauth_client');
        $definition->replaceArgument(0, $config['bitly']['username']);
        $definition->replaceArgument(1, $config['bitly']['password']);
    }
}