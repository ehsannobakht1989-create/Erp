<?php
declare(strict_types=1);

use Core\I18n\Jalali;

return function (): void {
    assertTrue(Jalali::isLeap(1403), '1403 is a Jalali leap year');
    assertTrue(!Jalali::isLeap(1402), '1402 is not a Jalali leap year');
    assertTrue(Jalali::isValidJalali(1403, 12, 30), '1403/12/30 is valid in leap year');
    assertTrue(!Jalali::isValidJalali(1402, 12, 30), '1402/12/30 is invalid in non-leap year');

    // Round-trip over sample years in 1300..1500
    foreach ([1300, 1357, 1399, 1403, 1404, 1450, 1500] as $jy) {
        foreach ([[1, 1], [6, 31], [7, 1], [12, 29]] as [$jm, $jd]) {
            [$gy, $gm, $gd] = Jalali::toGregorian($jy, $jm, $jd);
            [$ry, $rm, $rd] = Jalali::toJalali($gy, $gm, $gd);
            assertEquals([$jy, $jm, $jd], [$ry, $rm, $rd], "Round-trip failed for $jy/$jm/$jd");
        }
    }
};
