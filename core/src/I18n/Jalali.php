<?php
declare(strict_types=1);

namespace Core\I18n;

final class Jalali
{
    public static function isLeap(int $jy): bool
    {
        $breaks = [
            -61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181,
            1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178,
        ];
        $bl = count($breaks);
        $jp = $breaks[0];
        $jump = 0;
        for ($i = 1; $i < $bl; $i++) {
            $jm = $breaks[$i];
            $jump = $jm - $jp;
            if ($jy < $jm) {
                break;
            }
            $jp = $jm;
        }
        $n = $jy - $jp;
        if ($jump - $n < 6) {
            $n = $n - $jump + ((int) (($jump + 4) / 33)) * 33;
        }
        $leap = (($n + 1) % 33) - 1;
        if ($leap === -1) {
            $leap = 4;
        }
        return ($leap % 4) === 0;
    }

    /** @return array{0: int, 1: int, 2: int} [jy, jm, jd] */
    public static function toJalali(int $gy, int $gm, int $gd): array
    {
        $gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) + $gd + $gdm[$gm - 1];
        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;
        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }
        return [$jy, $jm, $jd];
    }

    /** @return array{0: int, 1: int, 2: int} [gy, gm, gd] */
    public static function toGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + (intdiv($jy, 33) * 8) + intdiv(($jy % 33) + 3, 4) + $jd + (($jm < 7) ? (($jm - 1) * 31) : ((($jm - 7) * 30) + 186));
        $gy = 400 * intdiv($days, 146097);
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * intdiv(--$days, 36524);
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            }
        }
        $gy += 4 * intdiv($days, 1461);
        $days %= 1461;
        if ($days > 365) {
            $gy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;
        $salA = [0, 31, (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gm = 0;
        for ($gm = 0; $gm < 13 && $gd > $salA[$gm]; $gm++) {
            $gd -= $salA[$gm];
        }
        return [$gy, $gm, $gd];
    }

    public static function isValidJalali(int $jy, int $jm, int $jd): bool
    {
        if ($jy < 1 || $jm < 1 || $jm > 12 || $jd < 1) {
            return false;
        }
        if ($jm <= 6) {
            return $jd <= 31;
        }
        if ($jm <= 11) {
            return $jd <= 30;
        }
        return $jd <= (self::isLeap($jy) ? 30 : 29);
    }

    public static function format(string $gregorianDate): string
    {
        $ts = strtotime($gregorianDate);
        if ($ts === false) {
            return $gregorianDate;
        }
        [$jy, $jm, $jd] = self::toJalali((int) date('Y', $ts), (int) date('n', $ts), (int) date('j', $ts));
        return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    }
}
