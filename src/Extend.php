<?php

namespace Mizmoz\Config;

use Mizmoz\Config\Contract\EnvironmentInterface;
use Mizmoz\Config\Contract\Resolver\ExtendInterface;
use Mizmoz\Config\Contract\ResolverInterface;

/**
 * Class Extend
 * @package Mizmoz\Config
 *
 * @method static Extend production(string $name, array $config) Extend the production config
 * @method static Extend staging(string $name, array $config) Extend the staging config
 * @method static Extend testing(string $name, array $config) Extend the testing config
 * @method static Extend development(string $name, array $config) Extend the development config
 */
class Extend implements ExtendInterface
{
    /**
     * @var string
     */
    private string $directory;

    /**
     * @var string
     */
    private string $environment;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * Extend constructor.
     * @param string $environment
     * @param string $name
     * @param array<string, mixed> $config
     */
    public function __construct(string $environment, string $name, array $config = [])
    {
        $this->environment = $environment;
        $this->name = $name;
        $this->config = $config;
    }

    /**
     * Extend the item.
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        // add the name to the arguments
        array_unshift($arguments, $name);

        // create the new extended instance
        $reflection = new \ReflectionClass(static::class);
        $extend = $reflection->newInstanceArgs($arguments);

        // find the directory we're calling extend from as this is probably the config directory
        return $extend->setConfigDirectory(dirname(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file']));
    }

    /**
     * @inheritDoc
     */
    public function resolve(): ResolverInterface|array
    {
        $configFile = $this->directory . '/' . $this->name .
            ($this->environment === EnvironmentInterface::ENV_PRODUCTION ? '' : '.' . $this->environment) . '.php';

        $config = require $configFile;

        // ensure the value has been resolved...
        while ($config instanceof ResolverInterface) {
            $config = $config->resolve();
        }

        return array_replace_recursive($config, $this->config);
    }

    /**
     * @inheritDoc
     */
    public function setConfigDirectory(string $directory): ExtendInterface
    {
        $this->directory = $directory;
        return $this;
    }
}