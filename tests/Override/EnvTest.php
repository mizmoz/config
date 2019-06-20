<?php

namespace Mizmoz\Config\Tests\Override;

use Mizmoz\Config\Config;
use Mizmoz\Config\Environment;
use Mizmoz\Config\Override\Env;
use Mizmoz\Config\Tests\TestCase;

class EnvTest extends TestCase
{
    public function testReplaceConfigWithArgs()
    {
        $_ENV['MM_VERSION'] = '10.0';
        $_ENV['MM_APP_NAME'] = '"Args App"';
        $_ENV['MM_APP_URL'] = '\'mizmoz.com\'';

        $config = Config::fromEnvironment(Environment::create(realpath(__DIR__ . '/../')))
            ->addOverride(new Env());
        $this->assertSame('10.0', $config->get('version'));
        $this->assertSame('Args App', $config->get('app.name'));
        $this->assertSame('localhost', $config->get('db.default.host'));
        $this->assertSame('mizmoz.com', $config->get('app.url'));
        $this->assertSame('development', $config->get('environment.name'));
    }
}