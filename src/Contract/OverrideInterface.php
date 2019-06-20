<?php

namespace Mizmoz\Config\Contract;

/**
 * Override the config values
 *
 * @package Mizmoz\Config\Contract
 */
interface OverrideInterface
{
    /**
     * Override the values in the config
     *
     * @param ConfigInterface $config
     * @return ConfigInterface
     */
    public function override(ConfigInterface $config): ConfigInterface;
}