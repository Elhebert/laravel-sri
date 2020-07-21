<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;

class GenerateSriAssetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'subresource-integrity.base_path' => './tests/files',
            'app.asset_url' => 'tests/files',
        ]);
    }

    /** @test */
    public function it_generates_css_with_integrity()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.css');

        $this->assertStringContainsString('link', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
    }

    /** @test */
    public function it_generates_css_with_integrity_and_credentials()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.css', true);

        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
        $this->assertStringContainsString('crossorigin="use-credentials"', $asset_string);
        $this->assertStringContainsString('link', $asset_string);
    }

    /** @test */
    public function it_generates_css_with_integrity_and_attributes()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.css', false, 'rel="stylesheet"');

        $this->assertStringContainsString('link', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
        $this->assertStringContainsString('rel="stylesheet"', $asset_string);
    }

    /** @test */
    public function it_generates_css_with_integrity_credentials_and_attributes()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.css', true, 'rel="stylesheet"');

        $this->assertStringContainsString('link', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
        $this->assertStringContainsString('crossorigin="use-credentials"', $asset_string);
        $this->assertStringContainsString('rel="stylesheet"', $asset_string);
    }

    /** @test */
    public function it_generates_js_with_integrity()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.js'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.js');

        $this->assertStringContainsString('script', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
    }

    /** @test */
    public function it_generates_js_with_integrity_and_credentials()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.js'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.js', true);

        $this->assertStringContainsString('script', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
        $this->assertStringContainsString('crossorigin="use-credentials"', $asset_string);
    }

    /** @test */
    public function it_generates_js_with_integrity_and_attributes()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.js'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.js', false, 'type="application/javascript" async');

        $this->assertStringContainsString('script', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
        $this->assertStringContainsString('type="application/javascript" async', $asset_string);
    }

    /** @test */
    public function it_generates_js_with_integrity_credentials_and_attributes()
    {
        $hash = hash('sha256', file_get_contents('./tests/files/app.js'), true);
        $base64Hash = base64_encode($hash);

        $asset_string = Sri::asset('app.js', true, 'type="application/javascript" async');

        $this->assertStringContainsString('script', $asset_string);
        $this->assertStringContainsString('integrity="sha256-'.$base64Hash.'"', $asset_string);
        $this->assertStringContainsString('crossorigin="use-credentials"', $asset_string);
        $this->assertStringContainsString('type="application/javascript" async', $asset_string);
    }

    /** @test */
    public function it_throws_an_exception_if_file_extension_invalid()
    {
        $this->expectExceptionMessage('Invalid file');
        Sri::asset('app.jpeg');
    }
}
