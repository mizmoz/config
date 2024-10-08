<?php

namespace Mizmoz\Config\Tests;

use Mizmoz\Config\Config;
use Mizmoz\Config\Environment;
use Mizmoz\Config\Override\Env;

class ConfigTest extends TestCase
{
    use PreserveSystemGlobalsTrait;

    public function testCreateFromArray(): void
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

    public function testInvokeAccess(): void
    {
        $config = new Config([
            'app' => [
                'name' => 'Mizmoz Config'
            ]
        ]);

        $this->assertSame('Mizmoz Config', $config('app.name'));
        $this->assertSame(null, $config('app.version'));
        $this->assertSame(1, $config('app.version', 1));
    }

    public function testCreateFromDirectory(): void
    {
        $config = Config::fromDirectory((string)realpath(__DIR__ . '/config'));

        $this->assertSame('Super App', $config->get('app.name'));
        $this->assertSame('db.servers.com', $config->get('db.default.host'));
    }

    public function testAddNamespace(): void
    {
        $config = new Config();
        $config->addNamespace('cache', [
            'memcache' => [
                'host' => 'cache.host.com',
            ],
        ]);

        $this->assertSame('cache.host.com', $config->get('cache.memcache.host'));
    }

    public function testAddOverride(): void
    {
        $config = new Config([
            'version' => '1.1.1',
            'greeting' => 'hello',
            'app' => [
                'name' => 'Mizmoz Config',
                'url' => 'https://www.mizmoz.com/'
            ]
        ]);

        // set some environment variables
        $_ENV['MM_VERSION'] = '2.0.0';
        $_ENV['MM_APP_URL'] = 'https://www.github.com/mizmoz/config';

        // add the overrides
        $config->addOverride(new Env());

        $this->assertSame('2.0.0', $config->get('version'));
        $this->assertSame('hello', $config->get('greeting'));
        $this->assertSame('Mizmoz Config', $config->get('app.name'));
        $this->assertSame('https://www.github.com/mizmoz/config', $config->get('app.url'));
    }

    /**
     * Get the configuration from the supplied directory for the current environment
     */
    public function testGetEnvironmentConfig(): void
    {
        $config = Config::fromEnvironment(Environment::create(__DIR__));
        $this->assertSame('Super App', $config->get('app.name'));
        $this->assertSame('localhost', $config->get('db.default.host'));
        $this->assertSame('development', $config->get('environment.name'));
        $this->assertSame(__DIR__, $config->get('environment.projectRoot'));
    }

    /**
     * Test we can set values at run time
     */
    public function testSetValue(): void
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
     * Test setting a value when the config requires resolving
     */
    public function testSetValueWithResolver(): void
    {
        $config = Config::fromEnvironment(Environment::create(__DIR__))->set('app.name', 'Setter');
        $this->assertSame('Setter', $config->get('app.name'));
        $this->assertSame('localhost', $config->get('db.default.host'));
        $this->assertSame('development', $config->get('environment.name'));
    }

    /**
     * Test we can use the reference syntax to get values
     */
    public function testGetWithReference(): void
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
    public function testGetWithDeepReference(): void
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