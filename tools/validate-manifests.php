<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$manifestFiles = glob($root . '/modules/*/module.json') ?: [];

if (empty($manifestFiles)) {
    fwrite(STDERR, "ERROR: No module manifests found under modules/*/module.json\n");
    exit(1);
}

$errors = [];
$manifests = [];

foreach ($manifestFiles as $file) {
    $dirName = basename(dirname($file));
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        $errors[] = "[$dirName] Invalid JSON in module.json: " . json_last_error_msg();
        continue;
    }

    foreach (['id', 'name', 'version', 'category', 'level'] as $req) {
        if (!array_key_exists($req, $data)) {
            $errors[] = "[$dirName] Missing required field: $req";
        }
    }

    $id = $data['id'] ?? '';
    if (!is_string($id) || !preg_match('/^[a-z][a-z0-9-]*$/', $id)) {
        $errors[] = "[$dirName] Invalid id '$id' (must be kebab-case)";
    } elseif ($id !== $dirName) {
        $errors[] = "[$dirName] Manifest id '$id' does not match directory name '$dirName'";
    }

    if (!isset($data['name']) || !is_array($data['name']) || empty($data['name']['fa']) || empty($data['name']['en'])) {
        $errors[] = "[$dirName] 'name' must contain non-empty 'fa' and 'en' strings";
    }

    $version = $data['version'] ?? '';
    if (!is_string($version) || !preg_match('/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?(?:\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?$/', $version)) {
        $errors[] = "[$dirName] Invalid semver version '$version'";
    }

    if (!isset($data['level']) || !is_int($data['level']) || $data['level'] < 0 || $data['level'] > 6) {
        $errors[] = "[$dirName] 'level' must be an integer between 0 and 6";
    }

    foreach (['emits', 'listens'] as $evField) {
        if (isset($data[$evField]) && is_array($data[$evField])) {
            foreach ($data[$evField] as $ev) {
                if (!is_string($ev) || !preg_match('/^[a-z][a-z0-9-]*\.[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*$/', $ev)) {
                    $errors[] = "[$dirName] Invalid event name '$ev' in '$evField' (expected <module>.<entity>.<verb_past>)";
                }
            }
        }
    }

    if (isset($data['ai']['capabilities']) && is_array($data['ai']['capabilities'])) {
        foreach ($data['ai']['capabilities'] as $cap) {
            if (isset($cap['tools']) && is_array($cap['tools'])) {
                foreach ($cap['tools'] as $tool) {
                    if (!is_string($tool) || strpos($tool, $id . '.') !== 0) {
                        $errors[] = "[$dirName] AI tool '$tool' must be namespaced with '$id.'";
                    }
                }
            }
        }
    }

    $manifests[$id] = $data;
}

// Check requires and optional references exist
foreach ($manifests as $id => $data) {
    foreach (['requires', 'optional'] as $depField) {
        if (isset($data[$depField]) && is_array($data[$depField])) {
            foreach ($data[$depField] as $dep) {
                if (!isset($manifests[$dep])) {
                    $errors[] = "[$id] Unknown module '$dep' in '$depField'";
                }
            }
        }
    }
}

// Cycle detection on 'requires' using DFS (0 = unvisited, 1 = visiting, 2 = visited)
$state = [];
$dfs = function (string $node, array $stack) use (&$dfs, &$state, &$manifests, &$errors): void {
    $state[$node] = 1;
    $stack[] = $node;
    $deps = $manifests[$node]['requires'] ?? [];
    if (is_array($deps)) {
        foreach ($deps as $dep) {
            if (!isset($manifests[$dep])) {
                continue;
            }
            $s = $state[$dep] ?? 0;
            if ($s === 1) {
                $cycle = implode(' -> ', array_merge($stack, [$dep]));
                $errors[] = "Dependency cycle detected: $cycle";
            } elseif ($s === 0) {
                $dfs($dep, $stack);
            }
        }
    }
    $state[$node] = 2;
};

foreach (array_keys($manifests) as $id) {
    if (($state[$id] ?? 0) === 0) {
        $dfs($id, []);
    }
}

if (!empty($errors)) {
    foreach ($errors as $err) {
        fwrite(STDERR, "ERROR: $err\n");
    }
    fwrite(STDERR, sprintf("Validation failed with %d error(s).\n", count($errors)));
    exit(1);
}

echo sprintf("Validated %d module manifests successfully. Zero errors, zero cycles.\n", count($manifests));
