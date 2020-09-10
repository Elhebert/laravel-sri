<?php

namespace Elhebert\SubresourceIntegrity;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Elhebert\SubresourceIntegrity\Components\Link;
use Elhebert\SubresourceIntegrity\Components\Script;
use Elhebert\SubresourceIntegrity\Components\LinkMix;
use Elhebert\SubresourceIntegrity\Components\ScriptMix;

class SriServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Sri::class, function () {
            return new Sri(config('subresource-integrity.algorithm'));
        });

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

        Blade::component('sri.script', Script::class);
        Blade::component('sri.script.mix', ScriptMix::class);
        Blade::component('sri.link', Link::class);
        Blade::component('sri.link.mix', LinkMix::class);
    }
}
