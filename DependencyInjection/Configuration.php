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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $rootNode = $treeBuilder->root('mremi_url_shortener');

        $rootNode
            ->children()
                ->scalarNode('link_class')
                    ->defaultValue('Mremi\UrlShortener\Model\Link')->cannotBeEmpty()
                ->end()
                ->arrayNode('providers')
                    ->children()
                        ->arrayNode('bitly')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('username')->end()
                                ->scalarNode('password')->end()
                                ->arrayNode('options')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('connect_timeout')->defaultValue(1)->end()
                                        ->integerNode('timeout')->defaultValue(1)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('google')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('api_key')->defaultNull()->end()
                                ->arrayNode('options')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('connect_timeout')->defaultValue(1)->end()
                                        ->integerNode('timeout')->defaultValue(1)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function($v) { return true === $v['providers']['bitly']['enabled'] && empty($v['providers']['bitly']['username']); })
                ->thenInvalid('You must set a Bit.ly username or set the enabled flag to false.')
            ->end()
            ->validate()
                ->ifTrue(function($v) { return true === $v['providers']['bitly']['enabled'] && empty($v['providers']['bitly']['password']); })
                ->thenInvalid('You must set a Bit.ly password or set the enabled flag to false.')
            ->end();

        return $treeBuilder;
    }
}
