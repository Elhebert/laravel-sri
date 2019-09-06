<?php

namespace Elhebert\SubresourceIntegrity;

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
            $path = $this->clean_path($path);

            if (Str::startsWith($path, ['http', 'https', '//'])) {
                $href = $path;
            } else {
                $href = mix($path);
            }

            return $this->generate($path, $href, $crossOrigin);
        });

        Blade::directive('assetSri', function (string $path, bool $crossOrigin = false) {
            $path = $this->clean_path($path);

            if (Str::startsWith($path, ['http', 'https', '//'])) {
                $href = $path;
            } else {
                $href = asset($path);
            }

            return $this->generate($path, $href, $crossOrigin);
        });
    }

    /**
     * Remove apostrophe and quotation mark.
     * 
     * @param string  $path
     * @return string
     * 
     * @throws \Exception
     */
    private function clean_path($path): string
    {
        $values = ['\'', '"'];
        return str_replace($values, '', $path);
    }

    /**
     * Parse and generate the URL.
     * 
     * @param string  $path
     * @param string  $href
     * @param bool    $crossOrigin
     * @return void
     * 
     * @throws \Exception
     */
    private function generate(string $path, string $href, bool $crossOrigin)
    {
        $integrity = SriFacade::html($href, $crossOrigin);

        if (Str::endsWith($path, 'css')) {
            return $this->css($href, $integrity);
        } elseif (Str::endsWith($path, 'js')) {
            return $this->js($href, $integrity);
        } else {
            throw new \Exception('Invalid file');
        }
    }

    /**
     * Generate JS URL.
     * 
     * @param string $href
     * @param string $integrity
     * @return \Illuminate\Support\HtmlString string
     */
    private function js(string $href, string $integrity)
    {
        return new HtmlString("<script src='".$href."' ".$integrity."></script>");
    }

    /**
     * Generate CSS URL.
     * 
     * @param string $href
     * @param string $integrity
     * @return \Illuminate\Support\HtmlString string
     */
    private function css(string $href, string $integrity)
    {
        return new HtmlString("<link href='".$href."' rel='stylesheet' ".$integrity.">");
    }
}
