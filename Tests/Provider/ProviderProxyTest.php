<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\Tests\Provider;

use Mremi\UrlShortenerBundle\Provider\ProviderProxy;

/**
 * Tests ProviderProxy class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class ProviderProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProviderProxy
     */
    private $providerProxy;

    /**
     * @var object
     */
    private $provider;

    /**
     * Tests the shorten method
     */
    public function testShorten()
    {
        $link = $this->getMock('Mremi\UrlShortener\Model\LinkInterface');

        $this->provider
            ->expects($this->once())
            ->method('shorten')
            ->with($this->equalTo($link));

        $this->providerProxy->shorten($link);
    }

    /**
     * Tests the expand method
     */
    public function testExpand()
    {
        $link = $this->getMock('Mremi\UrlShortener\Model\LinkInterface');

        $this->provider
            ->expects($this->once())
            ->method('expand')
            ->with($this->equalTo($link));

        $this->providerProxy->expand($link);
    }

    /**
     * Test that the proxy will pass through additional
     * arguments to `shorten()`
     */
    public function testVariadicShorten()
    {
        $link = $this->getMock('Mremi\UrlShortener\Model\LinkInterface');

        $this->provider
            ->expects($this->once())
            ->method('shorten')
            ->with($this->equalTo($link), $this->equalTo('bar'), $this->equalTo(12));

        $this->providerProxy->shorten($link, 'bar', 12);
    }

    /**
     * Test that the proxy will pass through additional
     * arguments to `expand()`
     */
    public function testVariadicExpand()
    {
        $link = $this->getMock('Mremi\UrlShortener\Model\LinkInterface');

        $this->provider
            ->expects($this->once())
            ->method('expand')
            ->with($this->equalTo($link), $this->equalTo('foo'));

        $this->providerProxy->expand($link, 'foo');
    }

    /**
     * Initializes providerProxy & provider properties
     */
    protected function setUp()
    {
        $this->provider = $this->getMock('Mremi\UrlShortener\Provider\UrlShortenerProviderInterface');

        $stopwatchEvent = $this->getMockBuilder('Symfony\Component\Stopwatch\StopwatchEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $stopwatchEvent
            ->expects($this->once())
            ->method('stop');

        $stopwatch = $this->getMockBuilder('Symfony\Component\Stopwatch\StopWatch')
            ->setMethods(array('start'))
            ->getMock();

        $stopwatch
            ->expects($this->once())
            ->method('start')
            ->will($this->returnValue($stopwatchEvent));

        $this->providerProxy = new ProviderProxy($this->provider, $stopwatch);
    }
}
