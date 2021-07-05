<?php

namespace Elhebert\SubresourceIntegrity;

use Elhebert\SubresourceIntegrity\Console\SriCacheCommand;
use Elhebert\SubresourceIntegrity\Console\SriClearCommand;
use Elhebert\SubresourceIntegrity\Components\Link;
use Elhebert\SubresourceIntegrity\Components\Script;
use Elhebert\SubresourceIntegrity\Contracts\SriCacheManager as SriCacheManagerContract;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SriServiceProvider extends ServiceProvider
{
    protected $commands = [
        SriCacheCommand::class,
        SriClearCommand::class,
    ];

    public function register()
    {
        $this->app->bind(SriCacheManagerContract::class, SriCacheManager::class);
        $this->app->singleton(Sri::class, function ($app) {
            return new Sri(
                config('subresource-integrity.algorithm'),
                $this->app[SriCacheManagerContract::class]
            );
        });

        $this->app->alias(Sri::class, 'sri');

        $this->mergeConfigFrom(
            __DIR__.'/../config/subresource-integrity.php',
            'subresource-integrity'
        );

        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/subresource-integrity.php' => config_path('subresource-integrity.php'),
        ]);

        Blade::component('sri.script', Script::class);
        Blade::component('sri.link', Link::class);
    }
}
