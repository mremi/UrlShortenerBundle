<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test shortener command.
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TestShortenerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Tests shortener')
            ->setName('mremi:url-shortener:test');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $chainProvider  = $this->getChainProvider();

        foreach ($chainProvider->getProviders() as $provider) {
            $link = $this->getLinkManager()->create();
            $link->setLongUrl('http://www.google.com/');

            $provider->shorten($link);

            $output->writeln(sprintf('* %s provider:', $provider->getName()));
            $output->writeln(sprintf('    <info>Shorten <comment>http://www.google.com/</comment>:</info> %s', $link->getShortUrl()));

            $provider->expand($link);

            $output->writeln(sprintf('    <info>Expand <comment>%s</comment>:</info> %s', $link->getShortUrl(), $link->getLongUrl()));
        }
    }

    /**
     * Gets the chain provider service
     *
     * @return \Mremi\UrlShortener\Provider\ChainProvider
     */
    private function getChainProvider()
    {
        return $this->getContainer()->get('mremi_url_shortener.chain_provider');
    }

    /**
     * Gets the link manager service
     *
     * @return \Mremi\UrlShortener\Model\LinkManagerInterface
     */
    private function getLinkManager()
    {
        return $this->getContainer()->get('mremi_url_shortener.link_manager');
    }
}
