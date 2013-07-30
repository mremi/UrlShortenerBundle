<?php

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
            )));

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

        $this->manager->findOneByProviderAndShortUrl('google', 'http://goo.gl/fbsS', true);
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
            )));

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

        $this->manager->findOneByProviderAndLongUrl('google', 'http://www.google.com/', true);
    }

    /**
     * Initializes manager property
     */
    protected function setUp()
    {
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $this->chainProvider = $this->getMockBuilder('Mremi\UrlShortener\Provider\ChainProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this->getMockBuilder('Mremi\UrlShortenerBundle\Doctrine\LinkManager')
            ->setConstructorArgs(array('Mremi\UrlShortenerBundle\Entity\Link', $objectManager, $this->chainProvider))
            ->setMethods(array('findOneBy'))
            ->getMock();
    }
}
