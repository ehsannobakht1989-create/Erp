<?php
declare(strict_types=1);

namespace Modules\PartyMaster\Services;

final class PartyValidator
{
    public static function isValidNationalCode(string $code): bool
    {
        if (!preg_match('/^\d{10}$/', $code) || preg_match('/^(\d)\1{9}$/', $code)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $code[$i]) * (10 - $i);
        }
        $rem = $sum % 11;
        $check = (int) $code[9];
        return $rem < 2 ? $check === $rem : $check === (11 - $rem);
    }

    public static function isValidIban(string $iban): bool
    {
        $iban = strtoupper(str_replace(' ', '', $iban));
        if (!preg_match('/^IR\d{24}$/', $iban)) {
            return false;
        }
        $rearranged = substr($iban, 4) . '1827' . substr($iban, 2, 2);
        $mod = 0;
        for ($i = 0, $len = strlen($rearranged); $i < $len; $i++) {
            $mod = ($mod * 10 + (int) $rearranged[$i]) % 97;
        }
        return $mod === 1;
    }
}
