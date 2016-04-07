<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\Provider;

use Mremi\UrlShortener\Model\LinkInterface;
use Mremi\UrlShortener\Provider\UrlShortenerProviderInterface;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * Provider proxy class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class ProviderProxy implements UrlShortenerProviderInterface
{
    /**
     * @var UrlShortenerProviderInterface
     */
    private $provider;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var array
     */
    private $traces = array();

    /**
     * @var integer
     */
    private $counter = 1;

    /**
     * Constructor
     *
     * @param UrlShortenerProviderInterface $provider  A provider instance
     * @param Stopwatch                     $stopWatch A Stopwatch instance
     */
    public function __construct(UrlShortenerProviderInterface $provider, Stopwatch $stopWatch)
    {
        $this->provider  = $provider;
        $this->stopwatch = $stopWatch;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->provider->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function shorten(LinkInterface $link)
    {
        $event = $this->startProfiling($link->getLongUrl());

        call_user_func_array(array($this->provider, 'shorten'), func_get_args());

        $this->stopProfiling($event, $link->getShortUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function expand(LinkInterface $link)
    {
        $event = $this->startProfiling($link->getShortUrl());

        call_user_func_array(array($this->provider, 'expand'), func_get_args());

        $this->stopProfiling($event, $link->getLongUrl());
    }

    /**
     * Gets the traces
     *
     * @return array
     */
    public function getTraces()
    {
        return $this->traces;
    }

    /**
     * Starts the profiling
     *
     * @param string $url URL to transform
     *
     * @return StopwatchEvent
     */
    private function startProfiling($url)
    {
        $this->traces[$this->counter] = array(
            'submitted_url'   => $url,
            'transformed_url' => null,
            'duration'        => null,
            'memory_start'    => memory_get_usage(true),
            'memory_end'      => null,
            'memory_peak'     => null,
        );

        $name = sprintf('%s (provider: %s)', $url, $this->provider->getName());

        return $this->stopwatch->start($name);
    }

    /**
     * Stops the profiling
     *
     * @param StopwatchEvent $event A stopwatchEvent instance
     * @param string         $url   Transformed URL
     */
    private function stopProfiling(StopwatchEvent $event, $url)
    {
        $event->stop();

        $this->traces[$this->counter] = array_merge($this->traces[$this->counter], array(
            'transformed_url' => $url,
            'duration'        => $event->getDuration(),
            'memory_end'      => memory_get_usage(true),
            'memory_peak'     => memory_get_peak_usage(true),
        ));

        $this->counter++;
    }
}
