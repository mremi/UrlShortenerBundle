<?php

/*
 * This file is part of the Mremi\UrlShortenerBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\UrlShortenerBundle\Tests\DependencyInjection;

use Mremi\UrlShortenerBundle\DependencyInjection\MremiUrlShortenerExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Mremi URL shortener extension test class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class MremiUrlShortenerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $configuration;

    /**
     * Tests extension loading throws exception if link model class is empty
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The path "mremi_url_shortener.link_class" cannot contain an empty value, but got "".
     */
    public function testUrlShortenerLoadThrowsExceptionIfLinkModelClassEmpty()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['link_class'] = '';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Bit.ly's username is not set
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "mremi_url_shortener": You must set a Bit.ly username or set the enabled flag to false.
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessBitlyUsernameSet()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['bitly']['enabled'] = true;
        unset($config['providers']['bitly']['username']);
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Bit.ly's username is empty
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "mremi_url_shortener": You must set a Bit.ly username or set the enabled flag to false.
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessBitlyUsernameEmpty()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['bitly']['enabled'] = true;
        $config['providers']['bitly']['username'] = '';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Bit.ly's password is not set
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "mremi_url_shortener": You must set a Bit.ly password or set the enabled flag to false.
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessBitlyPasswordSet()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['bitly']['enabled'] = true;
        unset($config['providers']['bitly']['password']);
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Bit.ly's password is empty
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "mremi_url_shortener": You must set a Bit.ly password or set the enabled flag to false.
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessBitlyPasswordEmpty()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['bitly']['enabled'] = true;
        $config['providers']['bitly']['password'] = '';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Bit.ly's connect timeout is not numeric
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid type for path "mremi_url_shortener.providers.bitly.options.connect_timeout". Expected int, but got string.
     */
    public function testUrlShortenerLoadThrowsExceptionIfBitlyConnectTimeoutNotNumeric()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['bitly']['enabled'] = true;
        $config['providers']['bitly']['options']['connect_timeout'] = 'foo';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Bit.ly's timeout is not numeric
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid type for path "mremi_url_shortener.providers.bitly.options.timeout". Expected int, but got string.
     */
    public function testUrlShortenerLoadThrowsExceptionIfBitlyTimeoutNotNumeric()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['bitly']['enabled'] = true;
        $config['providers']['bitly']['options']['timeout'] = 'foo';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Google's connect timeout is not numeric
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid type for path "mremi_url_shortener.providers.google.options.connect_timeout". Expected int, but got string.
     */
    public function testUrlShortenerLoadThrowsExceptionIfGoogleConnectTimeoutNotNumeric()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['google']['enabled'] = true;
        $config['providers']['google']['options']['connect_timeout'] = 'foo';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if Google's timeout is not numeric
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid type for path "mremi_url_shortener.providers.google.options.timeout". Expected int, but got string.
     */
    public function testUrlShortenerLoadThrowsExceptionIfGoogleTimeoutNotNumeric()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['providers']['google']['enabled'] = true;
        $config['providers']['google']['options']['timeout'] = 'foo';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests services existence
     */
    public function testUrlShortenerLoadServicesWithDefaults()
    {
        $this->createConfiguration();

        $this->assertHasDefinition('mremi_url_shortener.bitly.oauth_client');
        $this->assertHasDefinition('mremi_url_shortener.bitly.provider');
        $this->assertHasDefinition('mremi_url_shortener.google.provider');
        $this->assertHasDefinition('mremi_url_shortener.chain_provider');
        $this->assertHasDefinition('mremi_url_shortener.link_manager.doctrine');
        $this->assertHasDefinition('mremi_url_shortener.link_manager');
        $this->assertHasDefinition('mremi_url_shortener.twig.url_shortener_extension');
    }

    /**
     * Cleanups the configuration
     */
    protected function tearDown()
    {
        $this->configuration = null;
    }

    /**
     * Creates a configuration
     */
    protected function createConfiguration()
    {
        $this->configuration = new ContainerBuilder;
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();

        $config['providers']['bitly']['enabled']  = true;
        $config['providers']['google']['enabled'] = true;

        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * Gets an empty config
     *
     * @return array
     */
    protected function getFullConfig()
    {
        $yaml = <<<EOF
link_class: Mremi\UrlShortener\Model\Link

providers:
    bitly:
        enabled:             false
        username:            your_bitly_username
        password:            your_bitly_password
        options:
            connect_timeout: 1
            timeout:         1

    google:
        enabled:             false
        api_key:             your_google_api_key
        options:
            connect_timeout: 1
            timeout:         1
EOF;
        $parser = new Parser;

        return $parser->parse($yaml);
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }
}
