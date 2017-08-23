<?php

namespace Mizmoz\Config\Tests;

use Mizmoz\Config\Environment;

class EnvironmentTest extends TestCase
{
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
    public function testGetEnvironmentFromFile()
    {
        $this->assertSame(Environment::ENV_DEVELOPMENT, Environment::get(__DIR__));
    }
}