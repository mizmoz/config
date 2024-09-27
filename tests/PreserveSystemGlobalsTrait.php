<?php

namespace Mizmoz\Config\Tests;

trait PreserveSystemGlobalsTrait
{
    private array $preserve = [];

    public function setUp(): void
    {
        $this->preserve['$_ENV'] = $_ENV;
        $this->preserve['$_SERVER'] = $_SERVER;
    }

    public function tearDown(): void
    {
        $_ENV = $this->preserve['$_ENV'];
        $_SERVER = $this->preserve['$_SERVER'];
    }
}