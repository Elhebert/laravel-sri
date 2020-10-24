<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Illuminate\Support\Facades\View;

class LinkTest extends TestCase
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

        $this->app->instance('path.public', dirname(__DIR__).'/files');

        $view = View::file(dirname(__DIR__).'/files/link.blade.php', ['mix' => false, 'crossOrigin' => null])->render();
        $expected = <<<'HTML'
        <link href="http://localhost/css/app.css" integrity="this-hash-is-valid" crossorigin="anonymous" rel="stylesheet" />
        HTML;

        $this->assertStringContainsString(
            $expected,
            $view
        );
    }

    /** @test */
    public function it_uses_mix_when_the_mix_attribute_is_passed()
    {
        config([
            'subresource-integrity.mix_sri_path' => './tests/files/mix-sri.json',
        ]);

        $this->app->instance('path.public', dirname(__DIR__).'/files');

        $view = View::file(dirname(__DIR__).'/files/link.blade.php', ['mix' => true, 'crossOrigin' => null])->render();
        $expected = <<<'HTML'
        <link href="/css/app.css?id=some-random-string" integrity="this-hash-is-valid" crossorigin="anonymous" rel="stylesheet" />
        HTML;

        $this->assertStringContainsString(
            $expected,
            $view
        );
    }

    /** @test */
    public function it_uses_the_crossorigin_attribute_if_passed()
    {
        config([
            'subresource-integrity.mix_sri_path' => './tests/files/mix-sri.json',
        ]);

        $this->app->instance('path.public', dirname(__DIR__).'/files');

        $view = View::file(dirname(__DIR__).'/files/link.blade.php', ['mix' => false, 'crossOrigin' => 'test'])->render();
        $expected = <<<'HTML'
        <link href="http://localhost/css/app.css" integrity="this-hash-is-valid" crossorigin="test" rel="stylesheet" />
        HTML;

        $this->assertStringContainsString(
            $expected,
            $view
        );
    }
}
