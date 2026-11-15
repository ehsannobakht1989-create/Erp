<?php
declare(strict_types=1);

namespace Core\Support;

use InvalidArgumentException;

final class Money
{
    public const SCALE = 4;

    public static function add(string $a, string $b, int $scale = self::SCALE): string
    {
        return bcadd(self::normalize($a), self::normalize($b), $scale);
    }

    public static function sub(string $a, string $b, int $scale = self::SCALE): string
    {
        return bcsub(self::normalize($a), self::normalize($b), $scale);
    }

    public static function mul(string $a, string $b, int $scale = self::SCALE): string
    {
        return bcmul(self::normalize($a), self::normalize($b), $scale);
    }

    public static function div(string $a, string $b, int $scale = self::SCALE): string
    {
        $nb = self::normalize($b);
        if (bccomp($nb, '0', self::SCALE) === 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return bcdiv(self::normalize($a), $nb, $scale);
    }

    public static function compare(string $a, string $b, int $scale = self::SCALE): int
    {
        return bccomp(self::normalize($a), self::normalize($b), $scale);
    }

    public static function round(string $amount, int $precision = 0, string $mode = 'half_up'): string
    {
        $n = self::normalize($amount);
        $negative = str_starts_with($n, '-');
        $abs = $negative ? substr($n, 1) : $n;

        if ($mode === 'floor') {
            $factor = bcpow('10', (string) $precision, 0);
            $scaled = bcmul($n, $factor, self::SCALE);
            $parts = explode('.', $scaled);
            $intPart = $parts[0];
            if ($negative && isset($parts[1]) && (int) $parts[1] > 0) {
                $intPart = bcsub($intPart, '1', 0);
            }
            return bcdiv($intPart, $factor, $precision);
        }

        // half_up
        $half = '0.' . str_repeat('0', $precision) . '5';
        $adjusted = bcadd($abs, $half, $precision + 2);
        $truncated = bcadd($adjusted, '0', $precision);
        return $negative && bccomp($truncated, '0', $precision) !== 0 ? '-' . $truncated : $truncated;
    }

    public static function normalize(string $amount): string
    {
        $trimmed = trim($amount);
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $trimmed)) {
            throw new InvalidArgumentException("Invalid money amount: '$amount'");
        }
        return $trimmed;
    }
}
