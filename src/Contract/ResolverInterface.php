<?php

namespace Mizmoz\Config\Contract;

interface ResolverInterface
{
    /**
     * Resolve the item and return the config array
     *
     * @return array<string, mixed>|ResolverInterface
     */
    public function resolve(): ResolverInterface|array;
}