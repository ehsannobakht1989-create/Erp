<?php

declare(strict_types=1);

namespace App\Support\Modules;

use App\Support\Validation\SchemaValidator;
use JsonException;
use RuntimeException;

final class ManifestValidator
{
    /** @return list<string> */
    public function validate(string $modulesPath, string $schemaPath): array
    {
        $schema = json_decode($this->read($schemaPath), true, 512, JSON_THROW_ON_ERROR);
        $errors = [];
        $files = glob(rtrim($modulesPath, '/').'/*/module.json') ?: [];
        if ($files === []) {
            return ['No module manifests found.'];
        }
        foreach ($files as $file) {
            $id = basename(dirname($file));
            try {
                $manifest = json_decode($this->read($file), false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                $errors[] = $id.': invalid JSON';
                continue;
            }
            $issues = (new SchemaValidator)->validate($manifest, $schema);
            foreach ($issues as $issue) {
                $errors[] = $id.': '.$issue;
            }
            if ($issues !== []) {
                continue;
            }
            if ($manifest->id !== $id) {
                $errors[] = $id.': id does not match directory';
            }
            foreach (array_merge($manifest->permissions ?? [], $manifest->emits ?? []) as $ownedName) {
                if (! str_starts_with($ownedName, $id.'.')) {
                    $errors[] = $id.': owned permission/event must start with '.$id.'.';
                }
            }
            foreach ($manifest->ai->capabilities ?? [] as $capability) {
                foreach ($capability->tools ?? [] as $tool) {
                    if (! str_starts_with($tool, $id.'.')) {
                        $errors[] = $id.': AI tool must start with '.$id.'.';
                    }
                }
            }
            foreach ($manifest->menu ?? [] as $menu) {
                if (isset($menu->permission) && ! in_array($menu->permission, $manifest->permissions ?? [], true)) {
                    $errors[] = $id.': menu permission is not declared';
                }
            }
        }
        if ($errors === []) {
            try {
                $catalog = ModuleCatalog::scan($modulesPath);
                foreach ($catalog->manifests() as $id => $manifest) {
                    foreach ($manifest['optional'] as $dependency) {
                        if (! isset($catalog->manifests()[$dependency])) {
                            $errors[] = $id.': unknown optional dependency '.$dependency;
                        }
                    }
                }
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $errors;
    }

    private function read(string $path): string
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Cannot read '.$path);
        }

        return $contents;
    }
}
