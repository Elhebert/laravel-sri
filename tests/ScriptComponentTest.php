<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Illuminate\Support\Facades\View;

class ScriptComponentTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->artisan('view:clear');

        parent::tearDown();
    }

    /** @test */
    public function it_renders_the_component()
    {
        config([
            'subresource-integrity.mix_sri_path' => './tests/files/mix-sri.json',
        ]);

        $this->app->instance('path.public', __DIR__.'/files');

        $view = View::file(__DIR__.'/files/script.blade.php', ['mix' => false])->render();

        $this->assertStringContainsString(
            '<script src="http://localhost/js/app.js" integrity="this-hash-is-valid"  />',
            $view
        );
    }

    /** @test */
    public function it_uses_mix_when_the_mix_attribute_is_passed()
    {
        config([
            'subresource-integrity.mix_sri_path' => './tests/files/mix-sri.json',
        ]);

        $this->app->instance('path.public', __DIR__.'/files');

        $view = View::file(__DIR__.'/files/script.blade.php', ['mix' => true])->render();

        $this->assertStringContainsString(
            '<script src="/js/app.js?id=some-random-string" integrity="this-hash-is-valid"  />',
            $view
        );
    }
}
