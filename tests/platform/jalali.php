<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Values\Jalali;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Console\Kernel;

function jalaliCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
foreach ([
    [[2024, 3, 20], [1403, 1, 1]], [[2025, 3, 20], [1403, 12, 30]], [[2025, 3, 21], [1404, 1, 1]],
    [[2021, 3, 20], [1399, 12, 30]], [[1979, 2, 11], [1357, 11, 22]], [[2026, 11, 16], [1405, 8, 25]],
    [[2000, 2, 29], [1378, 12, 10]], [[2026, 3, 21], [1405, 1, 1]], [[2026, 3, 20], [1404, 12, 29]],
] as [$gregorian, $jalali]) {
    jalaliCheck(Jalali::fromGregorian(...$gregorian) === $jalali, 'Wrong Jalali conversion for '.implode('-', $gregorian));
    jalaliCheck(Jalali::toGregorian(...$jalali) === $gregorian, 'Wrong Gregorian conversion for '.implode('-', $jalali));
}
jalaliCheck(Jalali::isLeapYear(1399) && Jalali::isLeapYear(1403) && ! Jalali::isLeapYear(1404) && ! Jalali::isLeapYear(1402), 'Leap years are wrong');
jalaliCheck(Jalali::monthLength(1404, 12) === 29 && Jalali::monthLength(1403, 12) === 30 && Jalali::monthLength(1404, 6) === 31 && Jalali::monthLength(1404, 7) === 30, 'Month lengths are wrong');
jalaliCheck(! Jalali::isValid(1404, 12, 30) && ! Jalali::isValid(1404, 13, 1) && ! Jalali::isValid(1404, 0, 1), 'Invalid dates accepted');
for ($day = CarbonImmutable::create(2015, 1, 1, 0, 0, 0, 'UTC'); $day->year < 2031; $day = $day->addDay()) {
    $jalali = Jalali::fromGregorian($day->year, $day->month, $day->day);
    jalaliCheck(Jalali::toGregorian(...$jalali) === [$day->year, $day->month, $day->day], 'Round trip failed for '.$day->toDateString());
}
$instant = CarbonImmutable::create(2026, 3, 20, 21, 0, 0, 'UTC');
jalaliCheck(Jalali::format($instant, 'Y/m/d H:i', 'Asia/Tehran') === '1405/01/01 00:30', 'Display timezone must determine Jalali day');
jalaliCheck(Jalali::format($instant, 'j F Y', 'Asia/Tehran', true) === '۱ فروردین ۱۴۰۵', 'Persian month/digit formatting is wrong');
jalaliCheck(Jalali::format($instant, '\Y Y', 'UTC') === 'Y 1404', 'Escaped format tokens must be literal');
jalaliCheck(Jalali::parse('۱۴۰۵/۰۱/۰۱ ۰۰:۳۰', 'Asia/Tehran')->equalTo($instant), 'Persian digits/timezone parse must return UTC instant');
jalaliCheck(Jalali::parse('1405-08-25')->timezoneName === 'UTC' && Jalali::parse('1405-08-25')->format('Y-m-d H:i') === '2026-11-15 20:30', 'Default display timezone parse must return UTC');
jalaliCheck(config('app.timezone') === 'UTC' && app()->getLocale() === 'fa', 'Platform must persist UTC and default to Persian');
foreach (['1404/12/30', '1405/13/01', '1405/01/01 24:00', 'tomorrow'] as $invalid) {
    try {
        Jalali::parse($invalid, 'Asia/Tehran');
        throw new RuntimeException('Invalid Jalali input accepted: '.$invalid);
    } catch (InvalidArgumentException) {
    }
}
echo "Jalali calendar tests passed.\n";
