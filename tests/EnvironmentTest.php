<?php

namespace Mizmoz\Config\Tests;

use Mizmoz\Config\Contract\EnvironmentInterface;
use Mizmoz\Config\Environment;
use Mizmoz\Config\Exception\UnknownEnvironmentException;

class EnvironmentTest extends TestCase
{
    use PreserveSystemGlobalsTrait;

    /**
     * Should return the default environment which is production
     */
    public function testGetDefaultEnvironment(): void
    {
        $this->assertSame(EnvironmentInterface::ENV_PRODUCTION, Environment::get('/tmp/thisshouldnotexistjustforthetest'));
    }

    /**
     * Test getting the environment from the .env file
     */
    public function testGetEnvironmentFromEnv(): void
    {
        $_ENV['ENVIRONMENT'] = EnvironmentInterface::ENV_STAGING;
        $this->assertSame(EnvironmentInterface::ENV_STAGING, Environment::get(__DIR__));
    }

    /**
     * Test getting the environment from the .env file
     */
    public function testGetEnvironmentFromFile(): void
    {
        $this->assertSame(EnvironmentInterface::ENV_DEVELOPMENT, Environment::get(__DIR__));
    }

    /**
     * Test getting an unknown environment from the .env file
     * Should throw an exception
     */
    public function testGetUnknownEnvironmentFromFile(): void
    {
        $this->expectException(UnknownEnvironmentException::class);
        $this->assertSame(
            EnvironmentInterface::ENV_DEVELOPMENT,
            Environment::create(
                __DIR__,
                EnvironmentInterface::ENV_PRODUCTION,
                [EnvironmentInterface::ENV_PRODUCTION],
                '.environment-unknown'
            )->name()
        );
    }

    /**
     * Manually create an environment
     */
    public function testCreateEnvironment(): void
    {
        $env = new Environment(EnvironmentInterface::ENV_TESTING, __DIR__);
        $this->assertSame('testing', $env->name());
        $this->assertSame(__DIR__, $env->projectRoot());
    }

    /**
     * Test getting the allowed environments
     */
    public function testGetAllowedEnvironments(): void
    {
        $env = new Environment(EnvironmentInterface::ENV_TESTING, __DIR__, [
            EnvironmentInterface::ENV_PRODUCTION,
            EnvironmentInterface::ENV_TESTING,
            EnvironmentInterface::ENV_DEVELOPMENT,
        ]);
        $this->assertSame([
            EnvironmentInterface::ENV_PRODUCTION,
            EnvironmentInterface::ENV_TESTING,
            EnvironmentInterface::ENV_DEVELOPMENT,
        ], $env->allowed());
    }

    /**
     * Test getting the environment name using the magic method
     */
    public function testGetEnvironmentName(): void
    {
        $env = new Environment(EnvironmentInterface::ENV_TESTING, __DIR__);
        $this->assertSame('testing', (string)$env);
    }
}