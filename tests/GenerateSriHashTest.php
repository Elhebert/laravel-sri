<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\SriCacheManager;
use Elhebert\SubresourceIntegrity\Sri;
use Elhebert\SubresourceIntegrity\SriFacade;
use Mockery;

class GenerateSriHashTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_has_no_content()
    {
        $this->expectExceptionMessage('file not found');
        SriFacade::hash('');
    }

    /** @test */
    public function it_correctly_hash_the_content_of_a_file()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha256-{$base64Hash}", SriFacade::hash('files/app.css'));
    }

    /** @test */
    public function it_fallback_to_sha256_when_incorrect_algorithm_is_given()
    {
        config([
            'subresource-integrity.algorithm' => 'invalid-algorithm',
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertStringContainsString('sha256', SriFacade::hash('files/app.css', true));
    }

    /** @test */
    public function it_can_hash_using_sha384()
    {
        config([
            'subresource-integrity.algorithm' => 'sha384',
        ]);

        $hash = hash('sha384', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha384-{$base64Hash}", SriFacade::hash('files/app.css'));
    }

    /** @test */
    public function it_can_hash_using_sha512()
    {
        config([
            'subresource-integrity.algorithm' => 'sha512',
        ]);

        $hash = hash('sha512', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha512-{$base64Hash}", SriFacade::hash('files/app.css'));
    }

    /** @test */
    public function it_returns_an_empty_string_when_disabled()
    {
        config([
            'subresource-integrity.enabled' => false,
        ]);

        $this->assertEquals('', SriFacade::hash('files/app.css'));
    }

    /** @test */
    public function it_caches_computed_hash()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $sriCache = Mockery::mock(SriCacheManager::class);

        $sriCache->shouldReceive('set')->once();
        $sriCache->shouldReceive('get')->once()->andReturnNull();

        $sri = new Sri(
            config('subresource-integrity.algorithm'),
            $sriCache
        );

        $this->assertEquals("sha256-{$base64Hash}", $sri->hash('files/app.css'));
    }

    /** @test */
    public function it_gets_cached_hash_when_existing()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $sriCache = Mockery::mock(SriCacheManager::class);

        $sriCache->shouldReceive('set')->once();
        $sriCache->shouldReceive('get')->once()->andReturnNull();
        $sriCache->shouldReceive('get')->once()->andReturn("sha256-{$base64Hash}");

        $sri = new Sri(
            config('subresource-integrity.algorithm'),
            $sriCache
        );

        $this->assertEquals("sha256-{$base64Hash}", $sri->hash('files/app.css'));

        $this->assertEquals("sha256-{$base64Hash}", $sri->hash('files/app.css'));
    }

    /** @test */
    public function it_returns_an_empty_string_when_hashing_third_party_assets()
    {
        $this->assertEquals('', SriFacade::hash('https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.12/vue.min.js'));
    }

    /** @test */
    public function it_can_hash_third_party_assets_when_enabled()
    {
        config([
            'subresource-integrity.dangerously_allow_third_party_assets' => true,
        ]);

        $hash = hash('sha256', file_get_contents('https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.12/vue.min.js'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha256-{$base64Hash}", SriFacade::hash('https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.12/vue.min.js'));
    }
}
