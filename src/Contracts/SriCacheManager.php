<?php

namespace Elhebert\SubresourceIntegrity\Contracts;

interface SriCacheManager extends \Psr\SimpleCache\CacheInterface
{
    /**
     * Get the path for the sri cache.
     *
     * @return string
     */
    public function getCachedSriPath();
}
