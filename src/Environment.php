<?php

namespace Mizmoz\Config;

use Mizmoz\Config\Contract\EnvironmentInterface;
use Mizmoz\Config\Exception\UnknownEnvironmentException;

class Environment implements EnvironmentInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @var array
     */
    private $allowed = [];

    /**
     * Create the environment
     *
     * @param string $name
     * @param string $projectRoot
     * @param array $allowed
     */
    public function __construct(string $name, string $projectRoot, array $allowed = [])
    {
        $this->name = $name;
        $this->projectRoot = $projectRoot;
        $this->allowed = $allowed;
    }

    /**
     * Create the environment using the default params and by searching the .environment file.
     *
     * @param string $projectRoot
     * @param string $default
     * @param array $allowed
     * @return EnvironmentInterface
     */
    public static function create(
        string $projectRoot, $default = self::ENV_PRODUCTION, array $allowed = []
    ): EnvironmentInterface
    {
        if (! $allowed) {
            $allowed = [
                self::ENV_PRODUCTION,
                self::ENV_STAGING,
                self::ENV_TESTING,
                self::ENV_DEVELOPMENT,
            ];
        }

        $projectRoot = realpath($projectRoot);

        $filename = $projectRoot . '/.environment';
        if (! $projectRoot || ! file_exists($filename)) {
            return new static($default, $projectRoot);
        }

        // get the environment name
        $name = file_get_contents($filename);

        if (! in_array($name, $allowed)) {
            throw new UnknownEnvironmentException(
                'Unknown environment "' . $name . '". Either add to the allowed list or provide one of: '
                . join(', ', $allowed)
            );
        }

        return new static($name, $projectRoot);
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
        return (static::create($projectRoot, $default, $allowed))->name();
    }

    /**
     * @inheritDoc
     */
    public function allowed(): array
    {
        return $this->allowed;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
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