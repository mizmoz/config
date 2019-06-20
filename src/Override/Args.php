<?php

namespace Mizmoz\Config\Override;

use Mizmoz\Config\Contract\ConfigInterface;
use Mizmoz\Config\Contract\OverrideInterface;

class Args implements OverrideInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * Args constructor.
     *
     * @param string $prefix
     */
    public function __construct(string $prefix = 'MM_')
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function override(ConfigInterface $config): ConfigInterface
    {
        foreach ($_SERVER['argv'] as $key) {
            if (mb_strpos($key, $this->prefix) === false) {
                continue;
            }

            list($key, $value) = mb_split('=', $key);

            // remove the prefix
            $key = mb_substr($key, mb_strlen($this->prefix));

            // convert to config naming
            $key = mb_ereg_replace('_', '.', mb_strtolower($key));

            // remove any quotes around the string
            $value = mb_ereg_replace('^(["\'])(.*?)(["\'])$', '\\2', $value);

            // set the config value
            $config->set($key, $value);
        }

        return $config;
    }
}