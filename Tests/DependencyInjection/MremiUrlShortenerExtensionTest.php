<?php

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
     * Tests extension loading throws exception if Bit.ly's username is not set
     *
     * @expectedException        \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "mremi_url_shortener": You must set a Bit.ly username or set the enabled flag to false.
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessBitlyUsernameSet()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();
        $config['bitly']['enabled'] = true;
        unset($config['bitly']['username']);
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
        $config['bitly']['enabled'] = true;
        $config['bitly']['username'] = '';
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
        $config['bitly']['enabled'] = true;
        unset($config['bitly']['password']);
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
        $config['bitly']['enabled'] = true;
        $config['bitly']['password'] = '';
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
        $this->assertHasDefinition('mremi_url_shortener.http.client_factory');
    }

    /**
     * Cleanups the configuration
     */
    protected function tearDown()
    {
        unset($this->configuration);
    }

    /**
     * Creates a configuration
     */
    protected function createConfiguration()
    {
        $this->configuration = new ContainerBuilder;
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getFullConfig();

        $config['bitly']['enabled']  = true;
        $config['google']['enabled'] = true;

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
bitly:
    enabled:  false
    username: your_bitly_username
    password: your_bitly_password

google:
    enabled: false
    api_key: your_google_api_key
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
