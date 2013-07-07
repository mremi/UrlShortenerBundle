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
     * Tests extension loading throws exception if bitly is not set
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessBitlySet()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getEmptyConfig();
        unset($config['bitly']);
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if username is not set
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessUsernameSet()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getEmptyConfig();
        unset($config['bitly']['username']);
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if username is empty
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUrlShortenerLoadThrowsExceptionIfUsernameEmpty()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getEmptyConfig();
        $config['bitly']['username'] = '';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if password is not set
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUrlShortenerLoadThrowsExceptionUnlessPasswordSet()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getEmptyConfig();
        unset($config['bitly']['password']);
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests extension loading throws exception if password is empty
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUrlShortenerLoadThrowsExceptionIfPasswordEmpty()
    {
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getEmptyConfig();
        $config['bitly']['password'] = '';
        $loader->load(array($config), new ContainerBuilder);
    }

    /**
     * Tests services existence
     */
    public function testUrlShortenerLoadServicesWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('mremi_url_shortener.bitly.shortener');
    }

    /**
     * Cleanups the configuration
     */
    protected function tearDown()
    {
        unset($this->configuration);
    }

    /**
     * Creates an empty configuration
     */
    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder;
        $loader = new MremiUrlShortenerExtension;
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * Gets an empty config
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
bitly:
    username: your_bitly_username
    password: your_bitly_password
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
