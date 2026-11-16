<?php

declare(strict_types=1);

namespace App\Support\Validation;

use LogicException;
use stdClass;

/** The deliberately small JSON Schema vocabulary used by module.schema.json. */
final class SchemaValidator
{
    /** @param array<string, mixed> $schema
     * @return list<string>
     */
    public function validate(mixed $value, array $schema, string $path = '$'): array
    {
        $supported = ['$schema', '$id', 'title', 'description', 'default', 'type', 'required', 'additionalProperties', 'properties', 'items', 'uniqueItems', 'enum', 'pattern', 'minLength', 'minimum', 'maximum'];
        foreach (array_keys($schema) as $keyword) {
            if (! in_array($keyword, $supported, true)) {
                throw new LogicException('Unsupported schema keyword: '.$keyword);
            }
        }
        $validType = match ($schema['type'] ?? null) {
            null => true,
            'object' => $value instanceof stdClass,
            'array' => is_array($value) && array_is_list($value),
            'string' => is_string($value),
            'integer' => is_int($value),
            'number' => is_int($value) || is_float($value),
            'boolean' => is_bool($value),
            default => throw new LogicException('Unsupported schema type'),
        };
        if (! $validType) {
            return [$path.': expected '.$schema['type']];
        }
        $errors = [];
        if (isset($schema['enum']) && ! in_array($value, $schema['enum'], true)) {
            $errors[] = $path.': value not in enum';
        }
        if (is_string($value)) {
            if (isset($schema['minLength']) && preg_match_all('/./us', $value) < $schema['minLength']) {
                $errors[] = $path.': string is too short';
            }
            if (isset($schema['pattern']) && preg_match('~'.$schema['pattern'].'~uD', $value) !== 1) {
                $errors[] = $path.': invalid pattern';
            }
        }
        if (is_int($value) || is_float($value)) {
            if (isset($schema['minimum']) && $value < $schema['minimum']) {
                $errors[] = $path.': below minimum';
            }
            if (isset($schema['maximum']) && $value > $schema['maximum']) {
                $errors[] = $path.': above maximum';
            }
        }
        if ($value instanceof stdClass) {
            foreach ($schema['required'] ?? [] as $field) {
                if (! property_exists($value, $field)) {
                    $errors[] = $path.'.'.$field.': required';
                }
            }
            foreach (get_object_vars($value) as $field => $entry) {
                if (isset($schema['properties'][$field])) {
                    array_push($errors, ...$this->validate($entry, $schema['properties'][$field], $path.'.'.$field));
                } elseif (($schema['additionalProperties'] ?? true) === false) {
                    $errors[] = $path.'.'.$field.': additional property';
                }
            }
        }
        if (is_array($value)) {
            $seen = [];
            foreach ($value as $index => $entry) {
                if (($schema['uniqueItems'] ?? false) && in_array($entry, $seen, false)) {
                    $errors[] = $path.'['.$index.']: duplicate item';
                }
                $seen[] = $entry;
                array_push($errors, ...$this->validate($entry, $schema['items'] ?? [], $path.'['.$index.']'));
            }
        }

        return $errors;
    }
}
