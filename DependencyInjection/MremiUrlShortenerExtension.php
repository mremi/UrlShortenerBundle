<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('chain.xml');
        $loader->load('twig.xml');

        $this->configureLinkManager($container, $config, $loader);
        $this->configureBitly($container, $config, $loader);
        $this->configureGoogle($container, $config, $loader);
        $this->configureProfiler($container, $config, $loader);
    }

    /**
     * Configures the Link Manager service
     *
     * @param ContainerBuilder $container A container builder instance
     * @param array            $config    An array of configuration
     * @param XmlFileLoader    $loader    An XML file loader instance
     */
    private function configureLinkManager(ContainerBuilder $container, array $config, XmlFileLoader $loader)
    {
        $loader->load('manager.xml');

        $linkClass = $config['link_class'];

        $suffix = 'Mremi\UrlShortener\Model\Link' === $linkClass ? 'default' : 'doctrine';

        $container->setAlias('mremi_url_shortener.link_manager', sprintf('mremi_url_shortener.link_manager.%s', $suffix));

        $definition = $container->findDefinition('mremi_url_shortener.link_manager');
        $definition->replaceArgument(1, $linkClass);
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
        if (false === $config['providers']['bitly']['enabled']) {
            return;
        }

        $loader->load('bitly.xml');

        $definition = $container->getDefinition('mremi_url_shortener.bitly.oauth_client');
        $definition->replaceArgument(0, $config['providers']['bitly']['username']);
        $definition->replaceArgument(1, $config['providers']['bitly']['password']);

        $definition = $container->getDefinition('mremi_url_shortener.bitly.provider');
        $definition->replaceArgument(1, $config['providers']['bitly']['options']);
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
        if (false === $config['providers']['google']['enabled']) {
            return;
        }

        $loader->load('google.xml');

        $definition = $container->getDefinition('mremi_url_shortener.google.provider');
        $definition->replaceArgument(0, $config['providers']['google']['api_key']);
        $definition->replaceArgument(1, $config['providers']['google']['options']);
    }

    /**
     * Configures the profiler service
     *
     * @param ContainerBuilder $container A container builder instance
     * @param array            $config    An array of configuration
     * @param XmlFileLoader    $loader    An XML file loader instance
     */
    private function configureProfiler(ContainerBuilder $container, array $config, XmlFileLoader $loader)
    {
        // for unit tests
        if (!$container->hasParameter('kernel.debug')) {
            return;
        }

        if (!$container->getParameter('kernel.debug')) {
            return;
        }

        $loader->load('profiler.xml');
    }
}
