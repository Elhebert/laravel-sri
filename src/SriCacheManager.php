<?php

namespace Elhebert\SubresourceIntegrity;

use Elhebert\SubresourceIntegrity\Contracts\SriCacheManager as SriCacheManagerContract;
use Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\Str;

class SriCacheManager implements SriCacheManagerContract
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The sri cache contents.
     *
     * @var array
     */
    private $cacheContents;

    /**
     * Create a new SriCacheManager Instance.
     *
     * @param Illuminate\Foundation\Application $app
     * @param Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Application $app, Filesystem $files)
    {
        $this->app = $app;

        $this->files = $files;

        $this->cacheContents = is_file($cachePath = $this->getCachedSriPath()) ? require $cachePath : [];
    }

    /**
     * Get the path for the sri cache.
     *
     * @return string
     */
    public function getCachedSriPath()
    {
        if (is_null($env = Env::get('SRI_CACHE'))) {
            return $this->app->bootstrapPath('cache/sri.php');
        } else {
            return Str::startsWith($env, ['/', '\\'])
                ? $env
                : $this->app->basePath($env);
        }
    }

    /**
     * Update the sri cache file.
     *
     * @return void
     */
    private function updateCacheFile()
    {
        $cachePath = $this->getCachedSriPath();
        $cacheDirectory = dirname($cachePath);

        if (! $this->files->isDirectory($cacheDirectory)) {
            $this->files->makeDirectory($cacheDirectory, 0755, true, true);
        }

        return $this->files->put(
            $cachePath,
            '<?php return '.var_export($this->cacheContents, true).';'.PHP_EOL
        );
    }

    /**
     * Get a hash from the cache.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->cacheContents[$key] ?? $default;
    }

    /**
     * Set a hash to the cache.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($key, $value)
    {
        $this->cacheContents[$key] = $value;

        return $this->updateCacheFile();
    }

    /**
     * Delete a hash from the cache.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function delete($key)
    {
        unset($this->cacheContents[$key]);

        return $this->updateCacheFile();
    }

    /**
     * Clear all hashes from the cache.
     *
     * @return bool
     */
    public function clear()
    {
        $this->cacheContents = [];

        return $this->files->delete($this->getCachedSriPath());
    }

    /**
     * Get multiple hashes from the cache.
     *
     * @param string $keys
     * @param mixed  $default
     *
     * @return array
     *
     * @throws \Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException
     */
    public function getMultiple($keys, $default = null)
    {
        if (! is_array($keys)) {
            throw new InvalidArgumentException;
        }

        $cacheValues = [];

        foreach ($keys as $key) {
            $cacheValues[$key] = $this->cacheContents[$key] ?? $default;
        }

        return $cacheValues;
    }

    /**
     * Set multiple hashes to the cache.
     *
     * @param string $values
     *
     * @return bool
     *
     * @throws \Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException
     */
    public function setMultiple($values)
    {
        if (! is_array($values) || ! Arr::isAssoc($values)) {
            throw new InvalidArgumentException;
        }

        foreach ($values as $key => $value) {
            $this->cacheContents[$key] = $value;
        }

        return $this->updateCacheFile();
    }

    /**
     * Delete multiple hashes from the cache.
     *
     * @param string $keys
     *
     * @return bool
     *
     * @throws \Elhebert\SubresourceIntegrity\Exceptions\InvalidArgumentException
     */
    public function deleteMultiple($keys)
    {
        if (! is_array($keys)) {
            throw new InvalidArgumentException;
        }

        foreach ($keys as $key) {
            unset($this->cacheContents[$key]);
        }

        return $this->updateCacheFile();
    }

    /**
     * Check if hash is in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->cacheContents);
    }
}
