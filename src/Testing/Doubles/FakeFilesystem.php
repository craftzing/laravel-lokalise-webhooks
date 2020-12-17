<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Testing\Doubles;

use Illuminate\Contracts\Filesystem\Filesystem;

final class FakeFilesystem implements Filesystem
{
    public string $root;
    private bool $failToWrite = false;

    public function __construct()
    {
        $this->root = __DIR__ . '/../../stubs/fake/path/';
    }

    public function failToWrite(): void
    {
        $this->failToWrite = true;
    }

    public function put($path, $contents, $options = []): bool
    {
        return ! $this->failToWrite;
    }

    public function path(string $fileName): string
    {
        return $this->root . $fileName;
    }

    public function exists($path): void
    {
        //
    }

    public function get($path): void
    {
        //
    }

    public function readStream($path): void
    {
        //
    }

    public function writeStream($path, $resource, array $options = [])
    {
        return ! $this->failToWrite;
    }

    public function getVisibility($path): void
    {
        //
    }

    public function setVisibility($path, $visibility): void
    {
        //
    }

    public function prepend($path, $data): void
    {
        //
    }

    public function append($path, $data): void
    {
        //
    }

    public function delete($paths): void
    {
        //
    }

    public function copy($from, $to): void
    {
        //
    }

    public function move($from, $to): void
    {
        //
    }

    public function size($path): void
    {
        //
    }

    public function lastModified($path): void
    {
        //
    }

    public function files($directory = null, $recursive = false): void
    {
        //
    }

    public function allFiles($directory = null): void
    {
        //
    }

    public function directories($directory = null, $recursive = false): void
    {
        //
    }

    public function allDirectories($directory = null): void
    {
        //
    }

    public function makeDirectory($path): void
    {
        //
    }

    public function deleteDirectory($directory): void
    {
        //
    }
}
