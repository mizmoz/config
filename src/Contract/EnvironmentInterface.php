<?php

namespace Mizmoz\Config\Contract;

interface EnvironmentInterface
{
    const string ENV_PRODUCTION = 'production';
    const string ENV_STAGING = 'staging';
    const string ENV_TESTING = 'testing';
    const string ENV_DEVELOPMENT = 'development';

    /**
     * Get a list of the allowed environments
     *
     * @return string[]
     */
    public function allowed(): array;

    /**
     * Get the current environment
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the project root
     *
     * @return string
     */
    public function projectRoot(): string;
}