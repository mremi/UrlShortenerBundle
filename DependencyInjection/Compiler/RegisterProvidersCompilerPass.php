<?php

namespace Mremi\UrlShortenerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the providers
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class RegisterProvidersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mremi_url_shortener.chain_provider')) {
            return;
        }

        $definition = $container->getDefinition('mremi_url_shortener.chain_provider');

        foreach ($container->findTaggedServiceIds('mremi_url_shortener.provider') as $id => $attributes) {
            $definition->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
