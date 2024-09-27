<?php

namespace Mizmoz\Config\Contract;

interface ConfigInterface
{
    /**
     * Add a namespaced config
     *
     * @param string $name
     * @param array<string, mixed>|ResolverInterface $config
     * @return ConfigInterface
     */
    public function addNamespace(string $name, ResolverInterface|array $config): ConfigInterface;

    /**
     * Add an override for the config such as cli argument or environment variables
     *
     * @param OverrideInterface $override
     * @return ConfigInterface
     */
    public function addOverride(OverrideInterface $override): ConfigInterface;

    /**
     * Get the config value or return the default value if none is set
     *
     * @param string $name
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function get(string $name, mixed $defaultValue = null): mixed;

    /**
     * Temporarily set a value for the sessions
     *
     * @param string $name
     * @param mixed $value
     * @return ConfigInterface
     */
    public function set(string $name, mixed $value): ConfigInterface;
}