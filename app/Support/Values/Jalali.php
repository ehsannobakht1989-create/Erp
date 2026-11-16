<?php

declare(strict_types=1);

namespace App\Support\Values;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

/** Arithmetic Jalali calendar (jalaali algorithm, valid for years -61..3177). */
final class Jalali
{
    private const BREAKS = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];

    private const MONTHS = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];

    /** @return array{0:int,1:int,2:int} */
    public static function fromGregorian(int $gy, int $gm, int $gd): array
    {
        if (! checkdate($gm, $gd, $gy)) {
            throw new InvalidArgumentException('Invalid Gregorian date.');
        }

        return self::fromDayNumber(self::gregorianToDayNumber($gy, $gm, $gd));
    }

    /** @return array{0:int,1:int,2:int} */
    public static function toGregorian(int $jy, int $jm, int $jd): array
    {
        if (! self::isValid($jy, $jm, $jd)) {
            throw new InvalidArgumentException('Invalid Jalali date.');
        }
        $calendar = self::calendar($jy, true);
        $day = self::gregorianToDayNumber($calendar['gy'], 3, $calendar['march']) + ($jm - 1) * 31 - intdiv($jm, 7) * ($jm - 7) + $jd - 1;

        return self::dayNumberToGregorian($day);
    }

    public static function isLeapYear(int $jy): bool
    {
        return self::calendar($jy, false)['leap'] === 0;
    }

    public static function monthLength(int $jy, int $jm): int
    {
        if ($jm < 1 || $jm > 12) {
            throw new InvalidArgumentException('Invalid Jalali month.');
        }

        return $jm <= 6 ? 31 : ($jm <= 11 || self::isLeapYear($jy) ? 30 : 29);
    }

    public static function isValid(int $jy, int $jm, int $jd): bool
    {
        return $jy >= self::BREAKS[0] && $jy < self::BREAKS[count(self::BREAKS) - 1] && $jm >= 1 && $jm <= 12 && $jd >= 1 && $jd <= self::monthLength($jy, $jm);
    }

    /** Tokens: Y m n d j H i s F. Instant is converted to the display timezone first. */
    public static function format(DateTimeInterface $instant, string $pattern = 'Y/m/d', ?string $timezone = null, bool $persianDigits = false): string
    {
        $local = CarbonImmutable::instance($instant)->setTimezone(new DateTimeZone($timezone ?? (string) config('erp.display_timezone', 'Asia/Tehran')));
        [$jy, $jm, $jd] = self::fromGregorian((int) $local->format('Y'), (int) $local->format('n'), (int) $local->format('j'));
        $values = ['Y' => sprintf('%04d', $jy), 'm' => sprintf('%02d', $jm), 'n' => (string) $jm, 'd' => sprintf('%02d', $jd), 'j' => (string) $jd, 'H' => $local->format('H'), 'i' => $local->format('i'), 's' => $local->format('s'), 'F' => self::MONTHS[$jm - 1]];
        $result = preg_replace_callback('/\\\\(.)|([YmndjHisF])/u', fn (array $m) => $m[1] !== '' ? $m[1] : $values[$m[2]], $pattern);
        if ($result === null) {
            throw new InvalidArgumentException('Invalid Jalali format pattern.');
        }

        return $persianDigits ? self::toPersianDigits($result) : $result;
    }

    /** Parses Y/m/d or Y-m-d with optional H:i[:s] in the display timezone; returns a UTC instant. */
    public static function parse(string $value, ?string $timezone = null): CarbonImmutable
    {
        if (preg_match('/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/D', trim(self::toLatinDigits($value)), $m) !== 1) {
            throw new InvalidArgumentException('Invalid Jalali date format.');
        }
        [$gy, $gm, $gd] = self::toGregorian((int) $m[1], (int) $m[2], (int) $m[3]);
        [$hour, $minute, $second] = [(int) ($m[4] ?? 0), (int) ($m[5] ?? 0), (int) ($m[6] ?? 0)];
        if ($hour > 23 || $minute > 59 || $second > 59) {
            throw new InvalidArgumentException('Invalid time.');
        }

        return CarbonImmutable::create($gy, $gm, $gd, $hour, $minute, $second, $timezone ?? (string) config('erp.display_timezone', 'Asia/Tehran'))->utc();
    }

    public static function toPersianDigits(string $value): string
    {
        return strtr($value, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    public static function toLatinDigits(string $value): string
    {
        return strtr($value, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
    }

    /** @return array{leap:int, gy:int, march:int} */
    private static function calendar(int $jy, bool $withoutLeap): array
    {
        $count = count(self::BREAKS);
        if ($jy < self::BREAKS[0] || $jy >= self::BREAKS[$count - 1]) {
            throw new InvalidArgumentException('Jalali year out of range.');
        }
        $gy = $jy + 621;
        $leapJ = -14;
        $jp = self::BREAKS[0];
        $jump = 0;
        for ($i = 1; $i < $count; $i++) {
            $jm = self::BREAKS[$i];
            $jump = $jm - $jp;
            if ($jy < $jm) {
                break;
            }
            $leapJ += intdiv($jump, 33) * 8 + intdiv($jump % 33, 4);
            $jp = $jm;
        }
        $n = $jy - $jp;
        $leapJ += intdiv($n, 33) * 8 + intdiv($n % 33 + 3, 4);
        if ($jump % 33 === 4 && $jump - $n === 4) {
            $leapJ++;
        }
        $leapG = intdiv($gy, 4) - intdiv((intdiv($gy, 100) + 1) * 3, 4) - 150;
        $march = 20 + $leapJ - $leapG;
        if ($withoutLeap) {
            return ['leap' => -1, 'gy' => $gy, 'march' => $march];
        }
        if ($jump - $n < 6) {
            $n = $n - $jump + intdiv($jump + 4, 33) * 33;
        }
        $leap = (($n + 1) % 33 - 1) % 4;

        return ['leap' => $leap === -1 ? 4 : $leap, 'gy' => $gy, 'march' => $march];
    }

    /** @return array{0:int,1:int,2:int} */
    private static function fromDayNumber(int $day): array
    {
        $gy = self::dayNumberToGregorian($day)[0];
        $jy = $gy - 621;
        $calendar = self::calendar($jy, false);
        $k = $day - self::gregorianToDayNumber($gy, 3, $calendar['march']);
        if ($k >= 0) {
            if ($k <= 185) {
                return [$jy, 1 + intdiv($k, 31), $k % 31 + 1];
            }
            $k -= 186;
        } else {
            $jy--;
            $k += 179;
            if ($calendar['leap'] === 1) {
                $k++;
            }
        }

        return [$jy, 7 + intdiv($k, 30), $k % 30 + 1];
    }

    private static function gregorianToDayNumber(int $gy, int $gm, int $gd): int
    {
        $day = intdiv(($gy + intdiv($gm - 8, 6) + 100100) * 1461, 4) + intdiv(153 * (($gm + 9) % 12) + 2, 5) + $gd - 34840408;

        return $day - intdiv(intdiv($gy + 100100 + intdiv($gm - 8, 6), 100) * 3, 4) + 752;
    }

    /** @return array{0:int,1:int,2:int} */
    private static function dayNumberToGregorian(int $day): array
    {
        $j = 4 * $day + 139361631;
        $j += intdiv(intdiv(4 * $day + 183187720, 146097) * 3, 4) * 4 - 3908;
        $i = intdiv($j % 1461, 4) * 5 + 308;
        $gd = intdiv($i % 153, 5) + 1;
        $gm = intdiv($i, 153) % 12 + 1;

        return [intdiv($j, 1461) - 100100 + intdiv(8 - $gm, 6), $gm, $gd];
    }
}
