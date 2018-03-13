<?php

namespace Mizmoz\Config;

use Mizmoz\Config\Contract\ConfigInterface;
use Mizmoz\Config\Contract\EnvironmentInterface;
use Mizmoz\Config\Contract\ResolverInterface;
use Mizmoz\Config\Exception\InvalidArgumentException;
use Mizmoz\Config\Exception\NamespaceAlreadyExistsException;
use Mizmoz\Config\Resolver\File;

class Config implements ConfigInterface
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Parse any references in the name
     *
     * @param $name
     * @return string
     */
    private function parseReferences($name): string
    {
        if (preg_match_all('/\$\{(.*[a-z\.0-9])\}/si', $name, $results, PREG_SET_ORDER)) {
            foreach ($results as $match) {
                $replace = $match[0];
                $key = $match[1];

                // Handle using relative placement like get('db.${.default}.host');
                if (strpos($key, '.') === 0) {
                    // using relative placement
                    $key = substr($name, 0, strpos($name, $replace) - 1) . $key;
                }

                // get the value
                $value = $this->get($key);
                if ($value === null || $value === '' || $value === false) {
                    throw new InvalidArgumentException(
                        $key . ' replacement for ' . $name . ' must not be null or empty'
                    );
                }

                $name = str_replace($replace, $value, $name);
            }
        }

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function addNamespace(string $name, $config): ConfigInterface
    {
        if (! is_array($config) && ! $config instanceof ResolverInterface) {
            throw new InvalidArgumentException(
                __METHOD__ . ' $config must be either an array or ' . ResolverInterface::class
            );
        }

        if (array_key_exists($name, $this->config)) {
            throw new NamespaceAlreadyExistsException('Cannot add namespace as it already exists: ' . $name);
        }

        // add the namespace
        $this->config[$name] = $config;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, $defaultValue = null)
    {
        // check for any references
        $name = $this->parseReferences($name);

        // split the name
        $parts = explode('.', $name);
        $namespace = array_shift($parts);

        if (! array_key_exists($namespace, $this->config)) {
            return $defaultValue;
        }

        // ensure the value has been resolved...
        while ($this->config[$namespace] instanceof ResolverInterface) {
            $this->config[$namespace] = $this->config[$namespace]->resolve();
        }

        $value = $this->config[$namespace];

        foreach ($parts as $key) {
            if (! array_key_exists($key, $value)) {
                return $defaultValue;
            }

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, $value): ConfigInterface
    {
        $parts = explode('.', $name);
        $config = &$this->config;

        foreach ($parts as $key) {
            if (! array_key_exists($key, $config)) {
                $config[$key] = [];
            }

            $config = &$config[$key];
        }

        // set the value
        $config = $value;

        return $this;
    }

    /**
     * Fetch the config from the directory
     *
     * @param string $directory
     * @param string $suffix
     * @return ConfigInterface
     */
    public static function fromDirectory(string $directory, string $suffix = '.php'): ConfigInterface
    {
        $config = [];
        foreach (new \DirectoryIterator($directory) as $file) {
            if ($file->isDot()) {
                continue;
            }

            $suffixLen = strlen($suffix);
            if (! (substr($file->getFilename(), -$suffixLen) === $suffix)) {
                continue;
            }

            // get the file namespace like app.php will be app.
            $namespace = substr($file->getFilename(), 0, -($suffixLen));

            // create the file resolver so we can resolve the
            $config[$namespace] = new File($file->getPathname());
        }

        return new static($config);
    }

    /**
     * From the environment object
     *
     * @param EnvironmentInterface $environment
     * @param string $directory To be relative to the environment project root use ./config or /configs for full path
     * @return ConfigInterface
     */
    public static function fromEnvironment(
        EnvironmentInterface $environment,
        string $directory = './config'
    ): ConfigInterface
    {
        $name = $environment->name();
        $projectRoot = $environment->projectRoot();

        // get the config directory
        $directory = (strpos($directory, '/') === 0 ? $directory : $projectRoot . substr($directory, 1));

        $config = [];
        foreach (new \DirectoryIterator($directory) as $file) {
            if ($file->isDot() || $file->getExtension() !== 'php') {
                continue;
            }

            // get the file parts
            $parts = explode('.', $file->getFilename());

            $namespace = $parts[0];
            $fileEnvironment = (count($parts) === 3 ? $parts[1] : EnvironmentInterface::ENV_PRODUCTION);

            if ($fileEnvironment === EnvironmentInterface::ENV_PRODUCTION && ! array_key_exists($namespace, $config)) {
                // use the production configs by default
                $config[$namespace] = new File($file->getPathname());
            }

            if ($fileEnvironment === $name) {
                // create the file resolver so we can resolve the
                $config[$namespace] = new File($file->getPathname());
            }
        }

        // add the environment variables to the config
        $config['environment'] = [
            'name' => $name,
            'projectRoot' => $projectRoot,
        ];

        return new static($config);
    }
}
