<?php

namespace Mizmoz\Config;

use Mizmoz\Config\Contract\EnvironmentInterface;
use Mizmoz\Config\Exception\UnknownEnvironmentException;

class Environment implements EnvironmentInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $projectRoot;

    /**
     * @var string[]
     */
    private array $allowed;

    /**
     * Create the environment
     *
     * @param string $name
     * @param string $projectRoot
     * @param string[] $allowed
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
     * @param string[] $allowed
     * @return EnvironmentInterface
     */
    public static function create(
        string $projectRoot,
        string $default = self::ENV_PRODUCTION,
        array $allowed = [],
        string $environmentFile = '.environment'
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

        // first check if the ENV has been set
        if (array_key_exists('ENVIRONMENT', $_ENV) && in_array($_ENV['ENVIRONMENT'], $allowed)) {
            return new self($_ENV['ENVIRONMENT'], $projectRoot);
        }

        // fallback to the file
        $filename = $projectRoot . '/' . $environmentFile;
        if (! $projectRoot || ! file_exists($filename)) {
            return new self($default, $projectRoot);
        }

        // get the environment name
        $name = file_get_contents($filename);

        if (! in_array($name, $allowed)) {
            throw new UnknownEnvironmentException(
                'Unknown environment "' . $name . '". Either add to the allowed list or provide one of: '
                . join(', ', $allowed)
            );
        }

        return new self($name, $projectRoot);
    }

    /**
     * Get the environment
     *
     * @param string $projectRoot
     * @param string $default
     * @param string[] $allowed
     * @return string
     */
    public static function get(string $projectRoot, string $default = self::ENV_PRODUCTION, array $allowed = []): string
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