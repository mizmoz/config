<?php

namespace Mizmoz\Config\Tests;

use Mizmoz\Config\Extend;

class ExtendTest extends TestCase
{
    /**
     * Test extending a config file
     */
    public function testExtending()
    {
        $extend = Extend::production('db', [
            'default' => [
                'host' => 'localhost',
            ]
        ]);

        // set the config directory
        $extend->setConfigDirectory(realpath(__DIR__ . '/config'));

        // get the config
        $config = $extend->resolve();

        $this->assertSame('root', $config['default']['user']);
        $this->assertSame('localhost', $config['default']['host']);
    }

    /**
     * By default calling Extend::production('db', ...) should find the config directory used
     */
    public function testExtendingFromConfigDirectory()
    {
        $extend = require realpath(__DIR__ . '/config') . '/' . 'db.development.php';
        $config = $extend->resolve();

        $this->assertSame('root', $config['default']['user']);
        $this->assertSame('localhost', $config['default']['host']);
    }
}