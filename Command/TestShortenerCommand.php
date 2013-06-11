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
        $shortener = $this->getBitlyShortener();

        $shortened = $shortener->shorten('http://www.google.com');

        $output->writeln(sprintf('<info>Testing to shorten http://www.google.com :</info> %s', $shortened));
        $output->writeln(sprintf('<info>Testing to expand %s :</info> %s', $shortened, $shortener->expand($shortened)));
    }

    /**
     * Gets the bitly shortener service
     *
     * @return \Mremi\UrlShortener\Bitly\BitlyShortener
     */
    private function getBitlyShortener()
    {
        return $this->getContainer()->get('mremi_url_shortener.bitly.shortener');
    }
}