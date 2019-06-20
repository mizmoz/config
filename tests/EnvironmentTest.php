<?php

namespace Mizmoz\Config\Tests;

use Mizmoz\Config\Environment;

class EnvironmentTest extends TestCase
{
    use PreserveSystemGlobalsTrait;

    /**
     * Should return the default environment which is production
     */
    public function testGetDefaultEnvironment()
    {
        $this->assertSame(Environment::ENV_PRODUCTION, Environment::get('/tmp/thisshouldnotexistjustforthetest'));
    }

    /**
     * Test getting the environment from the .env file
     */
    public function testGetEnvironmentFromEnv()
    {
        $_ENV['ENVIRONMENT'] = Environment::ENV_STAGING;
        $this->assertSame(Environment::ENV_STAGING, Environment::get(__DIR__));
    }

    /**
     * Test getting the environment from the .env file
     */
    public function testGetEnvironmentFromFile()
    {
        $this->assertSame(Environment::ENV_DEVELOPMENT, Environment::get(__DIR__));
    }

    /**
     * Manually create an environment
     */
    public function testCreateEnvironment()
    {
        $env = new Environment(Environment::ENV_TESTING, __DIR__);
        $this->assertSame('testing', $env->name());
        $this->assertSame(__DIR__, $env->projectRoot());
    }
}