<?php

namespace Elhebert\SubresourceIntegrity;

use Illuminate\Support\Facades\File;

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
        if ($this->existsInConfigFile($path)) {
            return config('subresource-integrity.hashes')[$path];
        }

        if ($this->mixFileExists()) {
            $json = json_decode(file_get_contents($this->jsonFilePath()));
            $prefixedPath = starts_with($path, '/') ? $path : "/{$path}";

            if (array_key_exists($prefixedPath, $json)) {
                return $json->$prefixedPath;
            }
        }

        $hash = hash($this->algorithm, $this->getFileContent($path), true);
        $base64Hash = base64_encode($hash);

        return "{$this->algorithm}-{$base64Hash}";
    }

    private function existsInConfigFile(string $path): bool
    {
        return array_key_exists($path, config('subresource-integrity.hashes'));
    }

    private function mixFileExists(): bool
    {
        return file_exists($this->jsonFilePath());
    }

    private function getFileContent(string $path): string
    {
        if (starts_with($path, ['http', 'https', '//'])) {
            $fileContent = file_get_contents($path);
        } else {
            $fileContent = file_get_contents(config('subresource-integrity.base_path') . "/{$path}");
        }

        if (!$fileContent) {
            throw new \Exception('file not found');
        }

        return $fileContent;
    }

    private function jsonFilePath(): string
    {
        return config('subresource-integrity.mix_sri_path');
    }
}
