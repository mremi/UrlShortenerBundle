<?php

namespace Mremi\UrlShortenerBundle\Twig;

use Mremi\UrlShortener\Provider\ChainProvider;

/**
 * Url shortener extension class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class UrlShortenerExtension extends \Twig_Extension
{
    /**
     * @var ChainProvider
     */
    private $chainProvider;

    /**
     * Constructor
     *
     * @param ChainProvider $chainProvider
     */
    public function __construct(ChainProvider $chainProvider)
    {
        $this->chainProvider = $chainProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('mremi_url_shorten', array($this, 'shorten')),
            new \Twig_SimpleFunction('mremi_url_expand', array($this, 'expand')),
        );
    }

    /**
     * Shortens the given long URL using given provider
     *
     * @param string $providerName A provider name
     * @param string $longUrl      URL to shorten
     *
     * @return string
     */
    public function shorten($providerName, $longUrl)
    {
        $link = $this->chainProvider->getProvider($providerName)->shorten($longUrl);

        return $link->getShortUrl();
    }

    /**
     * Expands the given short URL using given provider
     *
     * @param string $providerName A provider name
     * @param string $shortUrl     URL to expand
     *
     * @return string
     */
    public function expand($providerName, $shortUrl)
    {
        $link = $this->chainProvider->getProvider($providerName)->expand($shortUrl);

        return $link->getLongUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'url_shortener';
    }
}
