<?php

namespace Mremi\UrlShortenerBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;

use Mremi\UrlShortener\Model\LinkInterface;
use Mremi\UrlShortener\Model\LinkManager as BaseLinkManager;
use Mremi\UrlShortener\Provider\ChainProvider;

/**
 * Link manager class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class LinkManager extends BaseLinkManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ChainProvider
     */
    protected $chainProvider;

    /**
     * Constructor
     *
     * @param string        $class         The Link class namespace
     * @param ObjectManager $objectManager An object manager instance
     * @param ChainProvider $chainProvider A chain provider instance
     */
    public function __construct($class, ObjectManager $objectManager, ChainProvider $chainProvider)
    {
        parent::__construct($class);

        $this->objectManager = $objectManager;
        $this->chainProvider = $chainProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function save(LinkInterface $link, $flush = false)
    {
        $this->objectManager->persist($link);

        if ($flush) {
            $this->objectManager->flush();
        }
    }

    /**
     * Finds one link by a short URL. If link does not exist in storage system,
     * try to fetch it using the given provider.
     *
     * @param string $shortUrl     A short URL
     * @param string $providerName A provider name, only if you want to fetch data if does not exist
     *
     * @return LinkInterface|null
     */
    public function findOneByShortUrl($shortUrl, $providerName = null)
    {
        $link = $this->findOneBy(array(
            'shortUrl' => $shortUrl,
        ));

        if ($link) {
            return $link;
        }

        if (null === $providerName) {
            return null;
        }

        return $this->chainProvider->getProvider($providerName)->expand($shortUrl);
    }

    /**
     * Finds one link by a long URL. If link does not exist in storage system,
     * try to fetch it using the given provider.
     *
     * @param string $longUrl      A long URL
     * @param string $providerName A provider name, only if you want to fetch data if does not exist
     *
     * @return LinkInterface|null
     */
    public function findOneByLongUrl($longUrl, $providerName = null)
    {
        $link = $this->findOneBy(array(
            'longUrl' => $longUrl,
        ));

        if ($link) {
            return $link;
        }

        if (null === $providerName) {
            return null;
        }

        return $this->chainProvider->getProvider($providerName)->shorten($longUrl);
    }

    /**
     * Finds one link by a given array of criteria
     *
     * @param array $criteria An array of criteria
     *
     * @return LinkInterface
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Gets the repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository()
    {
        return $this->objectManager->getRepository($this->class);
    }
}
