<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the providers
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class RegisterProvidersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mremi_url_shortener.chain_provider')) {
            return;
        }

        $chainProviderDefinition = $container->getDefinition('mremi_url_shortener.chain_provider');

        foreach ($container->findTaggedServiceIds('mremi_url_shortener.provider') as $id => $attributes) {
            if ($container->hasDefinition('mremi_url_shortener.provider.data_collector')) {
                $originId = sprintf('%s.origin', $id);

                // make origin provider private
                $providerDefinition = $container->getDefinition($id);
                $providerDefinition->setPublic(false);

                // create new provider definition
                $container->setDefinition($originId, $providerDefinition);

                // remove old one
                $container->removeDefinition($id);

                // create proxy
                $providerProxyDefinition = new Definition('Mremi\UrlShortenerBundle\Provider\ProviderProxy');
                $providerProxyDefinition->addTag('mremi_url_shortener.provider');
                $providerProxyDefinition->addArgument(new Reference($originId));
                $providerProxyDefinition->addArgument(new Reference('debug.stopwatch'));

                $container->setDefinition($id, $providerProxyDefinition);
            }

            $chainProviderDefinition->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
