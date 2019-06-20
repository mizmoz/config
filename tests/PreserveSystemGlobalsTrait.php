<?php

namespace Mizmoz\Config\Tests;

trait PreserveSystemGlobalsTrait
{
    private $preserve = [];

    public function setUp()
    {
        $this->preserve['$_ENV'] = $_ENV;
        $this->preserve['$_SERVER'] = $_SERVER;
    }

    public function tearDown()
    {
        $_ENV = $this->preserve['$_ENV'];
        $_SERVER = $this->preserve['$_SERVER'];
    }
}