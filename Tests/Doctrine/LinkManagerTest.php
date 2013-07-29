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
     * Tests the findOneByShortUrl method with no fetch
     */
    public function testFindOneByShortUrlNoFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('shortUrl' => 'http://goo.gl/fbsS')));

        $this->chainProvider
            ->expects($this->never())
            ->method('getProvider');

        $this->manager->findOneByShortUrl('http://goo.gl/fbsS');
    }

    /**
     * Tests the findOneByShortUrl method with fetch
     */
    public function testFindOneByShortUrlFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('shortUrl' => 'http://goo.gl/fbsS')));

        $this->chainProvider
            ->expects($this->once())
            ->method('getProvider')
            ->with($this->equalTo('google'))
            ->will($this->returnValue($this->getMock('Mremi\UrlShortener\Provider\UrlShortenerProviderInterface')));

        $this->manager->findOneByShortUrl('http://goo.gl/fbsS', 'google');
    }

    /**
     * Tests the findOneByLongUrl method with no fetch
     */
    public function testFindOneByLongUrlNoFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('longUrl' => 'http://www.google.com/')));

        $this->chainProvider
            ->expects($this->never())
            ->method('getProvider');

        $this->manager->findOneByLongUrl('http://www.google.com/');
    }

    /**
     * Tests the findOneByLongUrl method with fetch
     */
    public function testFindOneByLongUrlFetch()
    {
        $this->manager
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('longUrl' => 'http://www.google.com/')));

        $this->chainProvider
            ->expects($this->once())
            ->method('getProvider')
            ->with($this->equalTo('google'))
            ->will($this->returnValue($this->getMock('Mremi\UrlShortener\Provider\UrlShortenerProviderInterface')));

        $this->manager->findOneByLongUrl('http://www.google.com/', 'google');
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
