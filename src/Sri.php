<?php

namespace Elhebert\SubresourceIntegrity;

use Exception;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Sri
{
    /** @var string */
    private $algorithm;

    /** @var string */
    private $jsonFilePath;

    public function __construct(string $algorithm)
    {
        $this->algorithm = in_array($algorithm, ['sha256', 'sha384', 'sha512'])
            ? $algorithm
            : 'sha256';
        $this->jsonFilePath = config('subresource-integrity.mix_sri_path');
    }

    public function html(string $path, bool $useCredentials = false): HtmlString
    {
        if (! config('subresource-integrity.enabled')) {
            return new HtmlString('');
        }

        try {
            $integrity = $this->hash($path);
        } catch (\Exception $e) {
            return new HtmlString('');
        }

        $crossOrigin = $useCredentials ? 'use-credentials' : 'anonymous';

        return new HtmlString('integrity="'.$integrity.'" crossorigin="'.$crossOrigin.'"');
    }

    public function hash(string $path): string
    {
        if (! config('subresource-integrity.enabled')) {
            return '';
        }

        if ($this->existsInConfigFile($path)) {
            return config('subresource-integrity.hashes')[$path];
        }

        if ($this->mixFileExists()) {
            $json = json_decode(file_get_contents($this->jsonFilePath));
            $prefixedPath = Str::startsWith($path, '/') ? $path : "/{$path}";

            if (property_exists($json, $prefixedPath)) {
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
        return file_exists($this->jsonFilePath);
    }

    private function getFileContent(string $path): string
    {
        try {
            if (Str::startsWith($path, ['http', 'https', '//'])) {
                $fileContent = file_get_contents($path);
            } else {
                $path = Str::startsWith($path, '/') ? $path : "/{$path}";
                $path = parse_url($path, PHP_URL_PATH);

                $fileContent = file_get_contents(config('subresource-integrity.base_path')."{$path}");
            }

            if (! $fileContent) {
                throw new \Exception('file not found');
            }
        } catch (Exception $exception) {
            throw new \Exception('file not found');
        }

        return $fileContent;
    }
}
