<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Illuminate\Support\Facades\View;

class BladeDirectiveTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->artisan('view:clear');

        parent::tearDown();
    }

    /** @test */
    public function it_renders_mixSri_directive()
    {
        config([
            'subresource-integrity.mix_sri_path' => './tests/files/mix-sri.json',
            'view.paths' => ['./tests/files'],
        ]);

        $this->app->instance('path.public', __DIR__.'/files');

        $view = View::make('mixSri-view', [
            'asset' => 'css/app.css',
            'useCredentials' => false,
            'attributes' => '',
        ])->render();

        $this->assertStringContainsString('this-hash-is-valid', $view);
    }

    /** @test */
    public function it_renders_assetSri_directive()
    {
        config([
            'subresource-integrity.base_path' => './tests/files',
            'view.paths' => ['./tests/files'],
            'app.asset_url' => 'tests/files',
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.js'), true);
        $base64Hash = base64_encode($hash);

        $view = View::make('assetSri-view', [
            'asset' => 'app.js',
            'useCredentials' => false,
            'attributes' => '',
        ])->render();

        $this->assertStringContainsString("integrity='sha256-{$base64Hash}'", $view);
    }
}
