<?php

namespace Elhebert\SubresourceIntegrity;

use Illuminate\Support\ServiceProvider;

class SriServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Sri::class, function () {
            return new Sri();
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
    }
}
