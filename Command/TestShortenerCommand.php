<?php

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
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Tests shortener')
            ->setName('mremi:url-shortener:test');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $chainProvider  = $this->getChainProvider();

        foreach ($chainProvider->getProviders() as $provider) {
            $shortened = $provider->shorten('http://www.google.com');

            $output->writeln(sprintf('<info>Testing to shorten http://www.google.com with %s provider:</info> %s', $provider->getName(), $shortened));
            $output->writeln(sprintf('<info>Testing to expand %s with %s provider:</info> %s', $shortened, $provider->getName(), $provider->expand($shortened)));
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
}
