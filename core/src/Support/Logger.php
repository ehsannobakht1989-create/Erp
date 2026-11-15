<?php
declare(strict_types=1);

namespace Core\Support;

final class Logger
{
    private string $correlationId;

    public function __construct(
        private readonly string $logDir,
        private readonly int $maxBytes = 5_242_880,
        ?string $correlationId = null,
    ) {
        $this->correlationId = $correlationId ?? bin2hex(random_bytes(6));
    }

    public function setCorrelationId(string $id): void
    {
        $this->correlationId = $id;
    }

    /** @param array<string, mixed> $context */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /** @param array<string, mixed> $context */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /** @param array<string, mixed> $context */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /** @param array<string, mixed> $context */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0775, true);
        }
        $file = rtrim($this->logDir, '/\\') . '/erp-' . gmdate('Y-m-d') . '.log';
        if (is_file($file) && filesize($file) > $this->maxBytes) {
            @rename($file, $file . '.' . time());
        }
        $line = sprintf(
            "[%s] %s [%s] %s %s\n",
            gmdate('Y-m-d\TH:i:s\Z'),
            strtoupper($level),
            $this->correlationId,
            $message,
            $context ? (string) json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}
