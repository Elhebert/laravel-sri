<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;

class GenerateSriHtmlTest extends TestCase
{
    /** @test */
    public function it_returns_an_empty_string_when_the_hash_fails()
    {
        $this->assertEquals('', Sri::html(''));
    }

    /** @test */
    public function it_generates_html_code_with_integrity()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertStringContainsString("integrity='sha256-{$base64Hash}'", Sri::html('files/app.css'));
    }

    /** @test */
    public function it_generate_html_code_with_credentials_and_integrity()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertStringContainsString("integrity='sha256-{$base64Hash}'", Sri::html('files/app.css', true));
        $this->assertStringContainsString("crossorigin='use-credentials'", Sri::html('files/app.css', true));
    }
}
