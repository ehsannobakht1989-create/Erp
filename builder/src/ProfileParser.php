<?php

declare(strict_types=1);

namespace Builder;

use RuntimeException;

final class ProfileParser
{
    /** @return array<string, mixed> */
    public static function parseFile(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Profile not found: $path");
        }
        $raw = (string) file_get_contents($path);
        if (str_ends_with($path, '.json')) {
            $data = json_decode($raw, true);
            if (! is_array($data)) {
                throw new RuntimeException("Invalid JSON profile: $path");
            }

            return $data;
        }

        // Lightweight YAML subset parser for our profile format
        $data = [];
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $pos = strpos($line, ':');
            if ($pos === false) {
                continue;
            }
            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));
            if (str_starts_with($val, '{') || str_starts_with($val, '[')) {
                $jsonLike = preg_replace('/([a-zA-Z0-9_-]+)\s*:/', '"$1":', $val);
                $decoded = json_decode((string) $jsonLike, true);
                if (is_array($decoded)) {
                    $data[$key] = $decoded;

                    continue;
                }
                if (str_starts_with($val, '[')) {
                    $inner = trim($val, '[]');
                    $data[$key] = $inner === '' ? [] : array_map(fn ($s) => trim($s, " \t\"'"), explode(',', $inner));

                    continue;
                }
            }
            $data[$key] = trim($val, "\"'");
        }

        return $data;
    }
}
