<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\Twig;

use Mremi\UrlShortener\Model\LinkManagerInterface;

/**
 * Url shortener extension class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class UrlShortenerExtension extends \Twig_Extension
{
    /**
     * @var LinkManagerInterface
     */
    private $linkManager;

    /**
     * Constructor
     *
     * @param LinkManagerInterface $linkManager
     */
    public function __construct(LinkManagerInterface $linkManager)
    {
        $this->linkManager = $linkManager;
    }

    /**
     * {@inheritdoc}
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
     *
     * @throws \Mremi\UrlShortener\Exception\InvalidApiResponseException
     */
    public function shorten($providerName, $longUrl)
    {
        $link = $this->linkManager->findOneByProviderAndLongUrl($providerName, $longUrl);

        return $link->getShortUrl();
    }

    /**
     * Expands the given short URL using given provider
     *
     * @param string $providerName A provider name
     * @param string $shortUrl     URL to expand
     *
     * @return string
     *
     * @throws \Mremi\UrlShortener\Exception\InvalidApiResponseException
     */
    public function expand($providerName, $shortUrl)
    {
        $link = $this->linkManager->findOneByProviderAndShortUrl($providerName, $shortUrl);

        return $link->getLongUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'url_shortener';
    }
}
