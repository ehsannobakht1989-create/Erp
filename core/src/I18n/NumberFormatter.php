<?php
declare(strict_types=1);

namespace Core\I18n;

final class NumberFormatter
{
    private const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    private const LATIN_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public static function toPersianDigits(string $input): string
    {
        return str_replace(self::LATIN_DIGITS, self::PERSIAN_DIGITS, $input);
    }

    public static function formatCurrency(string|int $amount, string $unit = 'IRR', bool $persianDigits = true): string
    {
        $num = number_format((float) $amount, 0, '.', ',');
        $suffix = $unit === 'IRT' ? ' تومان' : ' ریال';
        $out = $num . $suffix;
        return $persianDigits ? self::toPersianDigits($out) : $out;
    }

    public static function rialToToman(string $rial): string
    {
        return bcdiv($rial, '10', 0);
    }

    public static function tomanToRial(string $toman): string
    {
        return bcmul($toman, '10', 0);
    }
}
