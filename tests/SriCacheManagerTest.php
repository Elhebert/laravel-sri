<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\Contracts\SriCacheManager;

class SriCacheManagerTest extends TestCase
{
    /** @var \Elhebert\SubresourceIntegrity\Contracts\SriCacheManager */
    protected $sriCache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sriCache = $this->app->get(SriCacheManager::class);
    }

    /** @test */
    public function it_generates_cache_file_in_default_directory()
    {
        $this->sriCache->set('path/to/file', 'some-secret-hash');

        $this->assertFileExists($this->sriCache->getCachedSriPath());
    }

    /** @test */
    public function it_generates_cache_file_in_custom_relative_directory()
    {
        $_ENV['SRI_CACHE'] = './custom/cache/file.php';

        $this->sriCache->set('path/to/file', 'some-secret-hash');

        $this->assertStringContainsString('./custom/cache/file.php', $this->sriCache->getCachedSriPath());
        $this->assertFileExists($this->sriCache->getCachedSriPath());

        $this->app->get('files')->deleteDirectory($this->app->basePath('./custom'));
    }

    /** @test */
    public function it_generates_cache_file_in_custom_directory()
    {
        $_ENV['SRI_CACHE'] = '/custom/cache/file.php';

        $this->sriCache->set('path/to/file', 'some-secret-hash');

        $this->assertEquals('/custom/cache/file.php', $this->sriCache->getCachedSriPath());
        $this->assertFileExists($this->sriCache->getCachedSriPath());

        $this->app->get('files')->deleteDirectory('/custom/cache/file.php');

        $this->app->get('files')->deleteDirectory('/custom');
    }

    /** @test */
    public function it_gets_value_from_cache_file()
    {
        $key = 'path/to/file';
        $value = 'some-secret-hash';

        $this->sriCache->set($key, $value);

        $this->assertEquals($value, $this->sriCache->get($key));
    }

    /** @test */
    public function it_returns_empty_string_when_cache_key_doesnt_exists()
    {
        $this->assertEquals('', $this->sriCache->get('non/existing/key'));
    }

    /** @test */
    public function it_returns_default_when_cache_key_doesnt_exists()
    {
        $default = 'some-fall-back-hash';

        $this->assertEquals($default, $this->sriCache->get('non/existing/key', $default));
    }

    /** @test */
    public function it_sets_values_to_cache_file()
    {
        $key = 'path/to/file';
        $value = 'some-secret-hash';

        $this->sriCache->set($key, $value);

        $cacheContents = require $this->sriCache->getCachedSriPath();

        $this->assertArrayHasKey($key, $cacheContents);
        $this->assertEquals($value, $cacheContents[$key]);
    }

    /** @test */
    public function it_deletes_values_form_cache_file()
    {
        $key = 'path/to/file';
        $value = 'some-secret-hash';

        $this->sriCache->set($key, $value);

        $cacheContents = require $this->sriCache->getCachedSriPath();

        $this->assertArrayHasKey($key, $cacheContents);
        $this->assertEquals($value, $cacheContents[$key]);

        $this->sriCache->delete($key);

        $cacheContents = require $this->sriCache->getCachedSriPath();

        $this->assertArrayNotHasKey($key, $cacheContents);
    }

    /** @test */
    public function it_gets_multiple_values_to_cache_file()
    {
        $values = [
            '/path/to/foo' => 'some-secret-foo',
            '/path/to/bar' => 'some-secret-bar',
        ];

        $this->sriCache->setMultiple($values);

        $this->assertEquals($values, $this->sriCache->getMultiple(array_keys($values)));
    }

    /** @test */
    public function it_throws_exception_when_getting_multiple_values_with_invalid_argument_given()
    {
        $this->expectException(\Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException::class);

        $this->sriCache->getMultiple('invalid');
    }

    /** @test */
    public function it_sets_multiple_values_to_cache_file()
    {
        $values = [
            '/path/to/foo' => 'some-secret-foo',
            '/path/to/bar' => 'some-secret-bar',
        ];

        $this->sriCache->setMultiple($values);

        $cacheContents = require $this->sriCache->getCachedSriPath();

        $this->assertEquals($values, $cacheContents);
    }

    /** @test */
    public function it_throws_exception_when_setting_multiple_values_with_invalid_argument_given()
    {
        $this->expectException(\Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException::class);

        $this->sriCache->setMultiple('invalid');
    }

    /** @test */
    public function it_deletes_multiple_values_to_cache_file()
    {
        $values = [
            '/path/to/foo' => 'some-secret-foo',
            '/path/to/bar' => 'some-secret-bar',
        ];

        $this->sriCache->setMultiple($values);

        $cacheContents = require $this->sriCache->getCachedSriPath();

        $this->assertEquals($values, $cacheContents);

        $this->sriCache->deleteMultiple(array_keys($values));

        $cacheContents = require $this->sriCache->getCachedSriPath();

        $this->assertEmpty($cacheContents, implode('|', $cacheContents));
    }

    /** @test */
    public function it_throws_exception_when_deleting_multiple_values_with_invalid_argument_given()
    {
        $this->expectException(\Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException::class);

        $this->sriCache->deleteMultiple('invalid');
    }

    /** @test */
    public function it_returns_true_when_checking_if_key_exists()
    {
        $key = 'path/to/file';
        $value = 'some-secret-hash';

        $this->sriCache->set($key, $value);

        $this->assertEquals(true, $this->sriCache->has($key));
    }

    /** @test */
    public function it_returns_false_when_checking_if_key_doesnt_exists()
    {
        $this->assertEquals(false, $this->sriCache->has('some-key'));
    }
}
