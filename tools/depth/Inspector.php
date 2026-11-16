<?php

declare(strict_types=1);

namespace Erp\Depth;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/** Conservative, incomplete diagnostics; never reports a passing score. */
final class Inspector
{
    private const CODE_EXTENSIONS = ['php', 'js', 'jsx', 'ts', 'tsx', 'sql'];

    public function __construct(private readonly string $root) {}

    /** @return list<array<string, mixed>> */
    public function inspectAll(): array
    {
        $paths = glob($this->root.'/modules/*/module.json') ?: [];
        sort($paths, SORT_STRING);
        if ($paths === []) {
            throw new RuntimeException('No module manifests found');
        }
        $reports = [];
        foreach ($paths as $path) {
            $reports[] = $this->inspect(basename(dirname($path)));
        }

        return $reports;
    }

    /** @return array<string, mixed> */
    public function inspect(string $id): array
    {
        if (! preg_match('/^[a-z][a-z0-9-]*$/D', $id)) {
            throw new RuntimeException('Invalid module id');
        }
        $directory = $this->root.'/modules/'.$id;
        $manifestPath = $directory.'/module.json';
        if (! is_file($manifestPath)) {
            throw new RuntimeException("Module $id not found");
        }
        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        if (! is_array($manifest) || ($manifest['id'] ?? null) !== $id || ! in_array($manifest['status'] ?? null, ['stub', 'partial', 'done'], true)) {
            throw new RuntimeException("Module $id has an invalid manifest id or status");
        }
        $missing = [];
        if (! is_file($this->root.'/specs/'.$id.'.md')) {
            $missing[] = 'Specification missing: specs/'.$id.'.md';
        }
        foreach ([
            'entity_cov' => 'specification entities and fields versus migrations',
            'event_cov' => 'emitted events and registered idempotent handlers',
            'contract_cov' => 'contract implementations',
            'rule_cov' => 'named tests and assertions for each rule',
            'perm_cov' => 'permissions on API and UI routes',
            'ui_cov' => 'entity pages, routes and Persian translations',
            'ai_cov' => 'capability tools and off/fake-provider tests',
            'anti_gaming' => 'complete trivial-method detection, empty tests, CRUD ratios and backend mocks',
            'tests' => 'module test execution',
        ] as $category => $description) {
            $missing[] = "$category: assessment not yet implemented ($description)";
        }

        return [
            'id' => $id,
            'status' => $manifest['status'],
            'score' => null,
            'pass' => false,
            'missing' => $missing,
            'violations' => $this->scanCode($directory),
        ];
    }

    /** @return list<string> */
    private function scanCode(string $directory): array
    {
        $violations = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $relative = substr($file->getPathname(), strlen($this->root) + 1);
            if ($file->isLink()) {
                throw new RuntimeException("Symlink in module directory: $relative");
            }
            if (! $file->isFile() || ! in_array(strtolower($file->getExtension()), self::CODE_EXTENSIONS, true)) {
                continue;
            }
            $content = file_get_contents($file->getPathname());
            if ($content === false) {
                throw new RuntimeException("Cannot read $relative");
            }
            if (strtolower($file->getExtension()) === 'php') {
                foreach (PhpAnalyzer::trivialFunctions($content) as $function) {
                    $violations[] = $relative.':'.$function['line'].': '.$function['name'].' '.$function['reason'];
                }
            }
            $lines = preg_split('/\R/', $content);
            if ($lines === false) {
                throw new RuntimeException("Cannot scan $relative");
            }
            foreach ($lines as $index => $line) {
                if (preg_match_all('/\b(?:TODO|FIXME|NotImplemented|not\s+implemented|placeholder|stub|lorem|dummy)\b/i', $line, $matches)) {
                    foreach (array_unique($matches[0]) as $marker) {
                        $violations[] = "$relative:".($index + 1).": banned marker '$marker'";
                    }
                }
            }
        }
        sort($violations, SORT_STRING);

        return $violations;
    }
}
