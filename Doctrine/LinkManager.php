<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * Constructor
     *
     * @param ChainProvider $chainProvider A chain provider instance
     * @param string        $class         The Link class namespace
     * @param ObjectManager $objectManager An object manager instance
     */
    public function __construct(ChainProvider $chainProvider, $class, ObjectManager $objectManager)
    {
        parent::__construct($chainProvider, $class);

        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(LinkInterface $link, $flush = false)
    {
        $this->objectManager->persist($link);

        if ($flush) {
            $this->objectManager->flush($link);
        }
    }

    /**
     * Finds one link by a provider and a short URL. If link does not exist in
     * storage system, try to fetch it using the given provider.
     *
     * @param string $providerName A provider name, first used to retrieve data from database, or call him if not found
     * @param string $shortUrl     A short URL
     *
     * @return LinkInterface
     *
     * @throws \Mremi\UrlShortener\Exception\InvalidApiResponseException
     */
    public function findOneByProviderAndShortUrl($providerName, $shortUrl)
    {
        $link = $this->findOneBy(array(
            'providerName' => $providerName,
            'shortUrl'     => $shortUrl,
        ));

        if ($link) {
            return $link;
        }

        $link = parent::findOneByProviderAndShortUrl($providerName, $shortUrl);

        // link does not exist, save it
        $this->save($link, true);

        return $link;
    }

    /**
     * Finds one link by a provider and a long URL. If link does not exist in
     * storage system, try to fetch it using the given provider.
     *
     * @param string $providerName A provider name, first used to retrieve data from database, or call him if not found
     * @param string $longUrl      A long URL
     *
     * @return LinkInterface
     *
     * @throws \Mremi\UrlShortener\Exception\InvalidApiResponseException
     */
    public function findOneByProviderAndLongUrl($providerName, $longUrl)
    {
        $link = $this->findOneBy(array(
            'providerName' => $providerName,
            'longUrl'      => $longUrl,
        ));

        if ($link) {
            return $link;
        }

        $link = parent::findOneByProviderAndLongUrl($providerName, $longUrl);

        // link does not exist, save it
        $this->save($link, true);

        return $link;
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
