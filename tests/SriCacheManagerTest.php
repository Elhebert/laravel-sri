<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\Contracts\SriCacheManager;
use Illuminate\Support\Env;

class SriCacheManagerTest extends TestCase
{
    /** @test */
    public function it_generates_cache_file_in_default_directory()
    {
        $sriCache = $this->app->get(SriCacheManager::class);

        $sriCache->set('path/to/file', 'some-secret-hash');

        $this->assertFileExists($sriCache->getCachedSriPath());
    }

    /** @test */
    public function it_generates_cache_file_in_custom_directory()
    {
        $_ENV['SRI_CACHE'] = './custom/cache/file.php';

        $sriCache = $this->app->get(SriCacheManager::class);

        $sriCache->set('path/to/file', 'some-secret-hash');

        $this->assertStringContainsString('./custom/cache/file.php', $sriCache->getCachedSriPath());
        $this->assertFileExists($sriCache->getCachedSriPath());
    }
}
