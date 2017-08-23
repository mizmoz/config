<?php

namespace Mizmoz\Config\Contract\Resolver;

use Mizmoz\Config\Contract\ResolverInterface;

interface ExtendInterface extends ResolverInterface
{
    /**
     * Set the config directory
     *
     * @param string $directory
     * @return ExtendInterface
     */
    public function setConfigDirectory(string $directory): ExtendInterface;
}