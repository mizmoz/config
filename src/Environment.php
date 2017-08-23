<?php

namespace Mizmoz\Config;

use Mizmoz\Config\Contract\ConfigInterface;
use Mizmoz\Config\Contract\EnvironmentInterface;
use Mizmoz\Config\Exception\UnknownEnvironmentException;

class Environment implements EnvironmentInterface
{
    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @var string
     */
    private $default;

    /**
     * @var array
     */
    private $allowed = [];

    /**
     * @var string
     */
    private $name;

    /**
     * Init with the allowed environments
     *
     * @param string $projectRoot
     * @param string $default
     * @param array $allowed
     */
    public function __construct(string $projectRoot, $default = self::ENV_PRODUCTION, array $allowed = [])
    {
        if (! $allowed) {
            $allowed = [
                self::ENV_PRODUCTION,
                self::ENV_STAGING,
                self::ENV_TESTING,
                self::ENV_DEVELOPMENT,
            ];
        }

        $this->name = $this->setup($projectRoot, $default, $allowed);
    }

    /**
     * Get the environment
     *
     * @param string $projectRoot
     * @param string $default
     * @param array $allowed
     * @return string
     */
    public static function get(string $projectRoot, $default = self::ENV_PRODUCTION, array $allowed = []): string
    {
        return (new static($projectRoot, $default, $allowed))->name();
    }

    /**
     * Setup the environment
     *
     * @param string $projectRoot
     * @param string $default
     * @param array $allowed
     * @return string
     */
    private function setup(string $projectRoot, $default = self::ENV_PRODUCTION, array $allowed): string
    {
        $this->allowed = $allowed;
        $this->projectRoot = realpath($projectRoot);
        $this->default = $default;

        $filename = $this->projectRoot . '/.env';
        if (! $this->projectRoot || ! file_exists($filename)) {
            return $default;
        }

        // get the environment name
        $name = file_get_contents($filename);

        if (! in_array($name, $this->allowed)) {
            throw new UnknownEnvironmentException(
                'Unknown environment "' . $name . '". Either add to the allowed list or provide one of: '
                . join(', ', $this->allowed)
            );
        }

        return $name;
    }

    /**
     * Get the current environment
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the project root
     *
     * @return string
     */
    public function projectRoot(): string
    {
        return $this->projectRoot;
    }

    /**
     * Get the env
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name();
    }
}