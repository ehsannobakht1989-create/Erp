<?php
declare(strict_types=1);

namespace Builder;

use ZipArchive;

final class Packager
{
    public function __construct(private readonly string $root)
    {
    }

    /**
     * @param array<string, mixed> $resolved
     * @return array{zip: string, file_count: int, size_bytes: int}
     */
    public function package(array $resolved, bool $withDev = false): array
    {
        $slug = (string) ($resolved['factory']['slug'] ?? 'erp');
        $buildDir = $this->root . '/build/' . $slug;
        $distDir = $this->root . '/dist';
        @mkdir($buildDir, 0775, true);
        @mkdir($distDir, 0775, true);

        file_put_contents($buildDir . '/resolved.json', (string) json_encode($resolved, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Generate install/install.sql
        $sqlParts = [];
        foreach (glob($this->root . '/core/migrations/*.sql') ?: [] as $f) {
            $sqlParts[] = "-- core/" . basename($f) . "\n" . file_get_contents($f);
        }
        foreach ($resolved['modules'] as $id) {
            foreach (glob($this->root . "/modules/$id/backend/migrations/*.sql") ?: [] as $f) {
                $sqlParts[] = "-- $id/" . basename($f) . "\n" . file_get_contents($f);
            }
        }
        $installSql = implode("\n\n", $sqlParts);

        $modulesPhp = "<?php\ndeclare(strict_types=1);\n\nreturn " . var_export(['enabled' => $resolved['modules']], true) . ";\n";
        $report = "# Build Report: $slug\n\n"
            . "- Modules (" . count($resolved['modules']) . "): " . implode(', ', $resolved['modules']) . "\n"
            . "- Auto-added dependencies: " . (empty($resolved['auto_added']) ? 'none' : json_encode($resolved['auto_added'])) . "\n"
            . "- Python AI service needed: " . ($resolved['python_needed'] ? 'yes' : 'no') . "\n";

        $zipPath = $distDir . "/{$slug}-1.0.0.zip";
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip: $zipPath");
        }

        $zip->addFromString('config/modules.php', $modulesPhp);
        $zip->addFromString('install/install.sql', $installSql);
        $zip->addFromString('BUILD-REPORT.md', $report);

        $this->addDirToZip($zip, $this->root . '/core/src', 'core/src');
        $this->addDirToZip($zip, $this->root . '/core/contracts', 'core/contracts');
        $this->addDirToZip($zip, $this->root . '/public', 'public');

        // Always include every module's backend/src/Contracts/ so type-hints never fail
        foreach (glob($this->root . '/modules/*/backend/src/Contracts', GLOB_ONLYDIR) ?: [] as $contractsDir) {
            $modId = basename(dirname($contractsDir, 3));
            $this->addDirToZip($zip, $contractsDir, "modules/$modId/backend/src/Contracts");
        }

        foreach ($resolved['modules'] as $id) {
            $modRoot = $this->root . "/modules/$id";
            if (is_file("$modRoot/module.json")) {
                $zip->addFile("$modRoot/module.json", "modules/$id/module.json");
            }
            if (is_dir("$modRoot/backend/src")) {
                $this->addDirToZip($zip, "$modRoot/backend/src", "modules/$id/backend/src");
            }
            if (is_file("$modRoot/backend/routes.php")) {
                $zip->addFile("$modRoot/backend/routes.php", "modules/$id/backend/routes.php");
            }
        }

        $fileCount = $zip->numFiles;
        $zip->close();

        return [
            'zip' => $zipPath,
            'file_count' => $fileCount,
            'size_bytes' => (int) filesize($zipPath),
        ];
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $zipPrefix): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile()) {
                $rel = substr($file->getPathname(), strlen($dir) + 1);
                $zip->addFile($file->getPathname(), $zipPrefix . '/' . str_replace('\\', '/', $rel));
            }
        }
    }
}
