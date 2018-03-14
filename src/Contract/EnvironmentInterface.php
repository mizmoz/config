<?php

namespace Mizmoz\Config\Contract;

interface EnvironmentInterface
{
    const ENV_PRODUCTION = 'production';
    const ENV_STAGING = 'staging';
    const ENV_TESTING = 'testing';
    const ENV_DEVELOPMENT = 'development';

    /**
     * Get a list of the allowed environments
     *
     * @return array
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