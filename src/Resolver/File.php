<?php

namespace Mizmoz\Config\Resolver;

use Mizmoz\Config\Contract\ResolverInterface;

/**
 * Class File
 *
 * Very basic class to allow the Config to lazy load the config files.
 *
 * @package Mizmoz\Config\Resolver
 */
class File implements ResolverInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * File constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritdoc
     */
    public function resolve()
    {
        return require $this->filename;
    }
}
