<?php

namespace Mizmoz\Config\Tests\Override;

use Mizmoz\Config\Config;
use Mizmoz\Config\Environment;
use Mizmoz\Config\Override\Args;
use Mizmoz\Config\Tests\PreserveSystemGlobalsTrait;
use Mizmoz\Config\Tests\TestCase;

class ArgsTest extends TestCase
{
    use PreserveSystemGlobalsTrait;

    public function testReplaceConfigWithArgs()
    {
        $_SERVER['argv'][] = 'MM_VERSION=10.0';
        $_SERVER['argv'][] = 'MM_APP_NAME="Args App"';
        $_SERVER['argv'][] = 'MM_APP_URL=\'mizmoz.com\'';

        $config = Config::fromEnvironment(Environment::create(realpath(__DIR__ . '/../')))
            ->addOverride(new Args());
        $this->assertSame('10.0', $config->get('version'));
        $this->assertSame('Args App', $config->get('app.name'));
        $this->assertSame('localhost', $config->get('db.default.host'));
        $this->assertSame('mizmoz.com', $config->get('app.url'));
        $this->assertSame('development', $config->get('environment.name'));
    }
}