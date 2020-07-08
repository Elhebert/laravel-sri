<?php

namespace Elhebert\SubresourceIntegrity;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
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

        Blade::directive('mixSri', function ($arguments) {
            return "<?php echo app('".Sri::class."')->mix({$arguments}) ?>";
        });

        Blade::directive('assetSri', function ($arguments) {
            return "<?php echo app('".Sri::class."')->asset({$arguments}) ?>";
        });
    }
}
