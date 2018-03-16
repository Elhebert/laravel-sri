<?php

namespace Elhebert\SubresourceIntegrity;

use PHPUnit\Framework\TestCase;

function file_get_contents(string $path): ?string
{
    if ($path === '' || $path === 'path/') {
        return null;
    }

    return $path;
}

function config(string $key): string
{
    return 'path';
}

class SriTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_if_has_no_content()
    {
        $sri = new Sri('sha256');

        $this->expectExceptionMessage('file not found');
        $sri->hash('');
    }

    /**
     * @test
     */
    public function it_returns_an_empty_string_when_the_hash_fails()
    {
        $sri = new Sri('sha256');

        $this->assertEquals('', $sri->html(''));
    }

    /**
     * @test
     */
    public function it_generates_html_code_with_integrity()
    {
        $sri = new Sri('sha256');

        $hash = hash('sha256', 'http://test.css', true);
        $base64Hash = base64_encode($hash);

        $this->assertContains($base64Hash, $sri->html('http://test.css'));
    }
}
