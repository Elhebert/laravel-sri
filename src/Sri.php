<?php

namespace Elhebert\SubresourceIntegrity;

class Sri
{
    /** @var string */
    private $algorithm;

    public function __construct(string $algorithm)
    {
        $this->algorithm = in_array($algorithm, ['sha256', 'sha384', 'sha512'])
            ? $algorithm
            : 'sha256';
    }

    public function html(string $path, bool $useCredentials = false): string
    {
        try {
            $integrity = $this->hash($path);
        } catch (\Exception $e) {
            return '';
        }

        $crossOrigin = $useCredentials ? 'use-credentials' : 'anonymous';

        return "integrity={$integrity} crossorigin={$crossOrigin}";
    }

    public function hash(string $path): string
    {
        if (starts_with($path, ['http', 'https', '//'])) {
            $fileContent = file_get_contents($path);
        } else {
            $fileContent = file_get_contents(config('subresource-integrity.base_path')."/{$path}");
        }

        if (! $fileContent) {
            throw new \Exception('file not found');
        }

        $hash = hash($this->algorithm, $fileContent, true);
        $base64Hash = base64_encode($hash);

        return "{$this->algorithm}-{$base64Hash}";
    }
}
