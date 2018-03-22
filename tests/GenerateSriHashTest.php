<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;

class GenerateSriHashTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_has_no_content()
    {
        $this->expectExceptionMessage('file not found');
        Sri::hash('');
    }

    /** @test */
    public function it_correctly_hash_the_content_of_a_file()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha256-{$base64Hash}", Sri::hash('files/app.css'));
    }

    /** @test */
    public function it_fallback_to_sha256_when_incorrect_algorithm_is_given()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
            'subresource-integrity.algorithm' => 'invalid-algorithm',
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertContains('sha256', Sri::hash('files/app.css', true));
    }

    /** @test */
    public function it_can_hash_using_sha384()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
            'subresource-integrity.algorithm' => 'sha384',
        ]);

        $hash = hash('sha384', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha384-{$base64Hash}", Sri::hash('files/app.css'));
    }

    /** @test */
    public function it_can_hash_using_sha512()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
            'subresource-integrity.algorithm' => 'sha512',
        ]);

        $hash = hash('sha512', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha512-{$base64Hash}", Sri::hash('files/app.css'));
    }
}
