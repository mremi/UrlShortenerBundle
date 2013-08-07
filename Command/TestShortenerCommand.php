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
            $shortened = $provider->shorten('http://www.google.com/')->getShortUrl();

            $output->writeln(sprintf('* %s provider:', $provider->getName()));

            $output->writeln(sprintf('    <info>Shorten <comment>http://www.google.com/</comment>:</info> %s', $shortened));
            $output->writeln(sprintf('    <info>Expand <comment>%s</comment>:</info> %s', $shortened, $provider->expand($shortened)->getLongUrl()));
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
