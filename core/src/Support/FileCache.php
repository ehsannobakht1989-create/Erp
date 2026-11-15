<?php
declare(strict_types=1);

namespace Core\Support;

final class FileCache
{
    public function __construct(private readonly string $cacheDir)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->path($key);
        if (!is_file($file)) {
            return $default;
        }
        $raw = file_get_contents($file);
        $data = $raw !== false ? json_decode($raw, true) : null;
        if (!is_array($data) || ($data['expires_at'] ?? 0) < time()) {
            @unlink($file);
            return $default;
        }
        return $data['value'] ?? $default;
    }

    public function set(string $key, mixed $value, int $ttlSeconds = 300): void
    {
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0775, true);
        }
        $payload = [
            'expires_at' => time() + $ttlSeconds,
            'value' => $value,
        ];
        @file_put_contents($this->path($key), (string) json_encode($payload, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public function delete(string $key): void
    {
        $file = $this->path($key);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public function clear(): void
    {
        if (!is_dir($this->cacheDir)) {
            return;
        }
        foreach (glob(rtrim($this->cacheDir, '/\\') . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }
    }

    private function path(string $key): string
    {
        return rtrim($this->cacheDir, '/\\') . '/' . sha1($key) . '.cache';
    }
}
