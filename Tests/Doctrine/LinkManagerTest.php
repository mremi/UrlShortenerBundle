<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\Tests\Doctrine;

/**
 * Tests LinkManager class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class LinkManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var object
     */
    private $manager;

    /**
     * @var object
     */
    private $chainProvider;

    /**
     * Tests the findOneByProviderAndShortUrl method with no fetch
     */
    public function testFindOneByProviderAndShortUrlNoFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array(
                'providerName' => 'google',
                'shortUrl'     => 'http://goo.gl/fbsS',
            )))
            ->will($this->returnValue($this->getMock('Mremi\UrlShortener\Model\LinkInterface')));

        $this->chainProvider
            ->expects($this->never())
            ->method('getProvider');

        $this->manager->findOneByProviderAndShortUrl('google', 'http://goo.gl/fbsS');
    }

    /**
     * Tests the findOneByProviderAndShortUrl method with fetch
     */
    public function testFindOneByProviderAndShortUrlFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array(
                'providerName' => 'google',
                'shortUrl'     => 'http://goo.gl/fbsS',
            )));

        $this->chainProvider
            ->expects($this->once())
            ->method('getProvider')
            ->with($this->equalTo('google'))
            ->will($this->returnValue($this->getMock('Mremi\UrlShortener\Provider\UrlShortenerProviderInterface')));

        $this->manager
            ->expects($this->once())
            ->method('save');

        $this->manager->findOneByProviderAndShortUrl('google', 'http://goo.gl/fbsS');
    }

    /**
     * Tests the findOneByProviderAndLongUrl method with no fetch
     */
    public function testFindOneByProviderAndLongUrlNoFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array(
                'providerName' => 'google',
                'longUrl'      => 'http://www.google.com/',
            )))
            ->will($this->returnValue($this->getMock('Mremi\UrlShortener\Model\LinkInterface')));

        $this->chainProvider
            ->expects($this->never())
            ->method('getProvider');

        $this->manager->findOneByProviderAndLongUrl('google', 'http://www.google.com/');
    }

    /**
     * Tests the findOneByProviderAndLongUrl method with fetch
     */
    public function testFindOneByProviderAndLongUrlFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array(
                'providerName' => 'google',
                'longUrl'      => 'http://www.google.com/',
            )));

        $this->chainProvider
            ->expects($this->once())
            ->method('getProvider')
            ->with($this->equalTo('google'))
            ->will($this->returnValue($this->getMock('Mremi\UrlShortener\Provider\UrlShortenerProviderInterface')));

        $this->manager
            ->expects($this->once())
            ->method('save');

        $this->manager->findOneByProviderAndLongUrl('google', 'http://www.google.com/');
    }

    /**
     * Initializes chainProvider & manager properties
     */
    protected function setUp()
    {
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $this->chainProvider = $this->getMockBuilder('Mremi\UrlShortener\Provider\ChainProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this->getMockBuilder('Mremi\UrlShortenerBundle\Doctrine\LinkManager')
            ->setConstructorArgs(array($this->chainProvider, 'Mremi\UrlShortenerBundle\Tests\Entity\Link', $objectManager))
            ->setMethods(array('findOneBy', 'save'))
            ->getMock();
    }
}
