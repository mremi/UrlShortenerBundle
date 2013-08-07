<?php

namespace Mremi\UrlShortenerBundle;

use Mremi\UrlShortenerBundle\DependencyInjection\Compiler\RegisterProvidersCompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * MremiUrlShortenerBundle class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class MremiUrlShortenerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterProvidersCompilerPass);
    }
}
