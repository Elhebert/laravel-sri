<?php

namespace Elhebert\SubresourceIntegrity;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class SriServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Sri::class, function () {
            return new Sri(config('subresource-integrity.algorithm'));
        });

        $this->app->alias(Sri::class, 'sri');

        $this->mergeConfigFrom(
            __DIR__.'/../config/subresource-integrity.php',
            'subresource-integrity'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/subresource-integrity.php' => config_path('subresource-integrity.php'),
        ]);

        Blade::directive('mixSri', function (string $path, bool $crossOrigin = false) {
            if (Str::startsWith($path, ['http', 'https', '//'])) {
                $href = $path;
            } else {
                $href = mix($path);
            }

            $integrity = SriFacade::html($path, $crossOrigin);

            if (Str::endsWith($path, 'css')) {
                return "<link href='{$href}' rel='stylesheet' {$integrity}>";
            } elseif (Str::endsWith($path, 'js')) {
                return "<script src='{$href}' {$integrity}></script>";
            } else {
                throw new \Exception('Invalid file');
            }
        });

        Blade::directive('assetSri', function (string $path, bool $crossOrigin = false) {
            if (Str::startsWith($path, ['http', 'https', '//'])) {
                $href = $path;
            } else {
                $href = asset($path);
            }

            $integrity = SriFacade::html($path, $crossOrigin);

            if (Str::endsWith($path, 'css')) {
                return "<link href='{$href}' rel='stylesheet' {$integrity}>";
            } elseif (Str::endsWith($path, 'js')) {
                return "<script src='{$href}' {$integrity}></script>";
            } else {
                throw new \Exception('Invalid file');
            }
        });
    }
}
