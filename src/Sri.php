<?php

namespace Elhebert\SubresourceIntegrity;

class Sri
{
    public function __construct()
    {
    }

    public function html(string $path, bool $with_credentials = false): string
    {
        try {
            $integrity = $this->hash($path);
        } catch (\Exception $e) {
            return '';
        }

        $crossorigin = $with_credentials ? '=use-credentials' : '';

        return "integrity={$integrity} crossorigin{$crossorigin}";
    }

    public function hash(string $path): string
    {
        if (starts_with($path, 'http')) {
            $fileContent = file_get_contents($path);
        } else {
            $fileContent = file_get_contents(config('subresource-integrity.base_path') . "/{$path}");
        }

        if (!$fileContent) {
            throw new \Exception('file not found');
        }

        $hash = hash('sha256', $fileContent, true);
        $base64Hash = base64_encode($hash);

        return "sha256-{$base64Hash}";
    }
}
