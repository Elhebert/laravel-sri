<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\Contracts\SriCacheManager;
use Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException;

class SriClearCommandTest extends TestCase
{
    /** @test */
    public function it_clears_sri_cache()
    {
        $sriCache = $this->app->get(SriCacheManager::class);

        $sriCache->set('path/to/file', 'some-secret-hash');

        $this->assertFileExists($sriCache->getCachedSriPath());

        $this->artisan('sri:clear');

        $this->assertFileDoesNotExist($sriCache->getCachedSriPath());
    }
}
