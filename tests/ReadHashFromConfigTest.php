<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;

class ReadHashFromConfigTest extends TestCase
{
    /** @test */
    public function it_correctly_read_a_hash_from_the_config()
    {
        config([
            'subresource-integrity.hashes' => [
                'css/app.css' => 'this-hash-is-valid',
            ],
        ]);

        $this->assertEquals('this-hash-is-valid', Sri::hash('css/app.css'));
    }

    /** @test */
    public function it_fallback_to_generating_to_hash_if_not_present_in_the_config()
    {
        config([
            'subresource-integrity.base_path' => './tests/',
            'subresource-integrity.hashes' => [
                'css/app.css' => 'this-hash-is-valid',
            ],
        ]);

        $hash = hash('sha256', file_get_contents('./tests/files/app.css'), true);
        $base64Hash = base64_encode($hash);

        $this->assertEquals("sha256-{$base64Hash}", Sri::hash('files/app.css', true));
    }
}
