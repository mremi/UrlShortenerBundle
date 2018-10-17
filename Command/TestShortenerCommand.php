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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Mremi\UrlShortener\Provider\UrlShortenerProviderInterface;
use Mremi\UrlShortener\Provider\ChainProvider;
use Mremi\UrlShortener\Model\LinkManager;

/**
 * Test shortener command.
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TestShortenerCommand extends Command
{
    /**
     * @var ChainProvider
     */
    private $chainProvider;

    /**
     * @var LinkManager
     */
    private $linkManager;

    public function __construct(ChainProvider $chainProvider, LinkManager $linkManager)
    {
        parent::__construct();

        $this->chainProvider = $chainProvider;
        $this->linkManager = $linkManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Tests shortener')
            ->setName('mremi:url-shortener:test')
            ->addOption(
                'provider',
                null,
                InputOption::VALUE_OPTIONAL,
                'The short link provider'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providerName = $input->getOption('provider');
        if ($providerName) {
            $provider =  $this->chainProvider->getProvider($providerName);
            $this->shorten($provider, $output);

            return;
        }

        foreach ($this->chainProvider->getProviders() as $provider) {
            $this->shorten($provider, $output);
        }
    }


    private function shorten(UrlShortenerProviderInterface $provider, OutputInterface $output)
    {
        $link = $this->linkManager->create();
        $link->setLongUrl('http://www.google.com/');

        $provider->shorten($link);

        $output->writeln(sprintf('* %s provider:', $provider->getName()));
        $output->writeln(sprintf('<info>Shorten <comment>http://www.google.com/</comment>:</info> %s', $link->getShortUrl()));

        $provider->expand($link);

        $output->writeln(sprintf('<info>Expand <comment>%s</comment>:</info> %s', $link->getShortUrl(), $link->getLongUrl()));
    }
}
