<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;

class GenerateSriMixTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'subresource-integrity.mix_sri_path' => './tests/files/mix-sri.json',
            'view.paths' => ['./tests/files'],
        ]);

        $this->app->instance('path.public', __DIR__.'/files');
    }

    /** @test */
    public function it_generates_css_with_integrity()
    {
        $this->assertStringContainsString('this-hash-is-valid', Sri::mix('css/app.css'));
    }

    /** @test */
    public function it_generates_css_with_integrity_and_credentials()
    {
        $mix_string = Sri::mix('css/app.css', true);

        $this->assertStringContainsString('this-hash-is-valid', $mix_string);
        $this->assertStringContainsString("crossorigin='use-credentials'", $mix_string);
    }

    /** @test */
    public function it_generates_css_with_integrity_and_attributes()
    {
        $mix_string = Sri::mix('css/app.css', false, 'rel="stylesheet"');

        $this->assertStringContainsString('this-hash-is-valid', $mix_string);
        $this->assertStringContainsString('rel="stylesheet"', $mix_string);
    }

    /** @test */
    public function it_generates_css_with_integrity_credentials_and_attributes()
    {
        $mix_string = Sri::mix('css/app.css', true, 'rel="stylesheet"');

        $this->assertStringContainsString('this-hash-is-valid', $mix_string);
        $this->assertStringContainsString("crossorigin='use-credentials'", $mix_string);
        $this->assertStringContainsString('rel="stylesheet"', $mix_string);
    }

    /** @test */
    public function it_generates_js_with_integrity()
    {
        $this->assertStringContainsString('this-hash-is-valid', Sri::mix('js/app.js'));
    }

    /** @test */
    public function it_generates_js_with_integrity_and_credentials()
    {
        $mix_string = Sri::mix('js/app.js', true);

        $this->assertStringContainsString('this-hash-is-valid', $mix_string);
        $this->assertStringContainsString("crossorigin='use-credentials'", $mix_string);
    }

    /** @test */
    public function it_generates_js_with_integrity_and_attributes()
    {
        $mix_string = Sri::mix('js/app.js', false, 'type="application/javascript" async');

        $this->assertStringContainsString('this-hash-is-valid', $mix_string);
        $this->assertStringContainsString('type="application/javascript" async', $mix_string);
    }

    /** @test */
    public function it_generates_js_with_integrity_credentials_and_attributes()
    {
        $mix_string = Sri::mix('js/app.js', true, 'type="application/javascript" async');

        $this->assertStringContainsString('this-hash-is-valid', $mix_string);
        $this->assertStringContainsString("crossorigin='use-credentials'", $mix_string);
        $this->assertStringContainsString('type="application/javascript" async', $mix_string);
    }

    /** @test */
    public function it_throws_an_exception_if_file_extension_invalid()
    {
        $this->expectExceptionMessage('Invalid file');

        Sri::mix('app.jpeg');
    }
}
