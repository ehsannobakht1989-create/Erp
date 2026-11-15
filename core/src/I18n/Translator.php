<?php
declare(strict_types=1);

namespace Core\I18n;

final class Translator
{
    /** @var array<string, array<string, string>> */
    private array $messages = [];

    public function __construct(
        private readonly string $langDir,
        private string $locale = 'fa',
    ) {
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /** @param array<string, string|int|float> $replace */
    public function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $loc = $locale ?? $this->locale;
        if (!isset($this->messages[$loc])) {
            $file = rtrim($this->langDir, '/\\') . '/' . $loc . '.php';
            $this->messages[$loc] = is_file($file) ? (array) require $file : [];
        }
        $msg = $this->messages[$loc][$key] ?? $key;
        foreach ($replace as $k => $v) {
            $msg = str_replace(':' . $k, (string) $v, $msg);
        }
        return $msg;
    }
}
