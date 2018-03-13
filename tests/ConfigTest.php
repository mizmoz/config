<?php

namespace Mizmoz\Config\Tests;

use Mizmoz\Config\Config;
use Mizmoz\Config\Environment;
use Mizmoz\Config\Using;

class ConfigTest extends TestCase
{
    public function testCreateFromArray()
    {
        $config = new Config([
            'app' => [
                'name' => 'Mizmoz Config'
            ]
        ]);

        $this->assertSame('Mizmoz Config', $config->get('app.name'));
        $this->assertSame(null, $config->get('app.version'));
        $this->assertSame(1, $config->get('app.version', 1));
    }

    public function testCreateFromDirectory()
    {
        $config = Config::fromDirectory(realpath(__DIR__ . '/config'));

        $this->assertSame('Super App', $config->get('app.name'));
        $this->assertSame('db.servers.com', $config->get('db.default.host'));
    }

    public function testAddNamespace()
    {
        $config = new Config();
        $config->addNamespace('cache', [
            'memcache' => [
                'host' => 'cache.host.com',
            ],
        ]);

        $this->assertSame('cache.host.com', $config->get('cache.memcache.host'));
    }

    /**
     * Get the configuration from the supplied directory for the current environment
     */
    public function testGetEnvironmentConfig()
    {
        $config = Config::fromEnvironment(new Environment(__DIR__));
        $this->assertSame('Super App', $config->get('app.name'));
        $this->assertSame('localhost', $config->get('db.default.host'));
        $this->assertSame('development', $config->get('environment.name'));
        $this->assertSame(__DIR__, $config->get('environment.projectRoot'));
    }

    /**
     * Test we can set values at run time
     */
    public function testSetValue()
    {
        $config = new Config([
            'app' => [
                'name' => 'Mizmoz Config',
                'version' => '1.0.0',
            ]
        ]);

        $config->set('app.name', 'Test');

        $this->assertSame('Test', $config->get('app.name'));
        $this->assertSame('1.0.0', $config->get('app.version'));
    }

    /**
     * Test we can use the reference syntax to get values
     */
    public function testGetWithReference()
    {
        $config = new Config([
            'default' => 'mysql',
            'mysql' => 3306,
        ]);

        $this->assertSame(3306, $config->get('${default}'));
    }

    /**
     * Test we can use the reference syntax to get values
     */
    public function testGetWithDeepReference()
    {
        $config = new Config([
            'db' => [
                'default' => 'mysql',
                'extended' => [
                    'name' => 'mysql'
                ],
                'mysql' => 3306,
            ]
        ]);

        $this->assertSame(3306, $config->get('db.${db.default}'));

        // using relative syntax
        $this->assertSame(3306, $config->get('db.${.default}'));

        // using extended relative syntax
        $this->assertSame(3306, $config->get('db.${.extended.name}'));
    }
}