<?php
declare(strict_types=1);

namespace Core\Database;

use RuntimeException;

final class Migrator
{
    public function __construct(
        private readonly Connection $db,
        private readonly string $root,
    ) {
    }

    public function ensureTrackingTable(): void
    {
        $this->db->execute(
            'CREATE TABLE IF NOT EXISTS core__migrations (
                id INTEGER PRIMARY KEY ' . ($this->db->driver() === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ',
                module VARCHAR(64) NOT NULL,
                file VARCHAR(255) NOT NULL,
                checksum VARCHAR(64) NOT NULL,
                applied_at VARCHAR(32) NOT NULL,
                UNIQUE(module, file)
            )'
        );
    }

    /**
     * @param array<int, string> $enabledModules Topologically sorted module IDs
     * @return array<int, array{module: string, file: string, status: string}>
     */
    public function migrate(array $enabledModules = [], bool $dryRun = false): array
    {
        $this->ensureTrackingTable();
        $applied = $this->getAppliedMap();
        $results = [];

        $sources = ['core' => $this->root . '/core/migrations'];
        foreach ($enabledModules as $mod) {
            $sources[$mod] = $this->root . '/modules/' . $mod . '/backend/migrations';
        }

        foreach ($sources as $module => $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            $files = glob($dir . '/*.sql') ?: [];
            sort($files, SORT_STRING);

            foreach ($files as $path) {
                $file = basename($path);
                $sql = (string) file_get_contents($path);
                $checksum = hash('sha256', $sql);
                $key = $module . ':' . $file;

                if (isset($applied[$key])) {
                    if ($applied[$key] !== $checksum) {
                        throw new RuntimeException(
                            "Checksum drift detected for migration [$module/$file]: expected {$applied[$key]}, got $checksum"
                        );
                    }
                    $results[] = ['module' => $module, 'file' => $file, 'status' => 'skipped'];
                    continue;
                }

                if ($dryRun) {
                    $results[] = ['module' => $module, 'file' => $file, 'status' => 'pending'];
                    continue;
                }

                $this->db->transaction(function (Connection $conn) use ($sql, $module, $file, $checksum): void {
                    foreach ($this->splitStatements($sql) as $stmt) {
                        $conn->execute($stmt);
                    }
                    $conn->execute(
                        'INSERT INTO core__migrations (module, file, checksum, applied_at) VALUES (?, ?, ?, ?)',
                        [$module, $file, $checksum, gmdate('Y-m-d H:i:s')]
                    );
                });

                $results[] = ['module' => $module, 'file' => $file, 'status' => 'applied'];
            }
        }

        return $results;
    }

    /** @return array<int, array<string, mixed>> */
    public function status(): array
    {
        $this->ensureTrackingTable();
        return $this->db->fetchAll('SELECT module, file, checksum, applied_at FROM core__migrations ORDER BY id ASC');
    }

    /** @return array<string, string> */
    private function getAppliedMap(): array
    {
        $rows = $this->db->fetchAll('SELECT module, file, checksum FROM core__migrations');
        $map = [];
        foreach ($rows as $row) {
            $map[$row['module'] . ':' . $row['file']] = (string) $row['checksum'];
        }
        return $map;
    }

    /** @return array<int, string> */
    private function splitStatements(string $sql): array
    {
        $parts = array_filter(array_map('trim', explode(';', $sql)), fn(string $s): bool => $s !== '');
        return array_values($parts);
    }
}
