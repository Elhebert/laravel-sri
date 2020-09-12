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

    public function __construct(Application $app, Filesystem $files)
    {
        $this->app = $app;

        $this->files = $files;

        $this->cacheContents = is_file($cachePath = $this->getCachedSriPath()) ? require $cachePath : [];
    }

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

    public function updateCacheFile()
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

    public function get($key, $default = null)
    {
        return $this->cacheContents[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->cacheContents[$key] = $value;

        $this->updateCacheFile();
    }

    public function delete($key)
    {
        unset($this->cacheContents[$key]);

        $this->updateCacheFile();
    }

    public function clear()
    {
        return $this->files->delete($this->getCachedSriPath());
    }

    public function getMultiple($keys, $default = null)
    {
        if (! is_array($keys)) {
            throw new InvalidArgumentException;
        }

        $cacheValues = [];

        foreach ($keys as $key) {
            $cacheValues[] = $this->cacheContents[$key] ?? $default;
        }

        return $cacheValues;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (! Arr::isAssoc($values)) {
            throw new InvalidArgumentException;
        }

        foreach ($values as $key => $value) {
            $this->cacheContents[$key] = $value;
        }

        $this->updateCacheFile();
    }

    public function deleteMultiple($keys)
    {
        if (! is_array($keys)) {
            throw new InvalidArgumentException;
        }

        foreach ($keys as $key) {
            unset($this->cacheContents[$key]);
        }

        $this->updateCacheFile();
    }

    public function has($key)
    {
        return array_key_exists($key, $this->cacheContents);
    }
}
