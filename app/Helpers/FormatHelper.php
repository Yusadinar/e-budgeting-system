<?php

namespace App\Helpers;

class FormatHelper
{
    public static function rupiah($value)
    {
        $val = (float) $value;
        if (abs($val) >= 1000000000) {
            $res = number_format($val / 1000000000, 2, ',', '.');
            if (str_ends_with($res, ',00')) {
                $res = substr($res, 0, -3);
            } elseif (str_ends_with($res, '0')) {
                $res = substr($res, 0, -1);
            }
            return 'Rp ' . $res . ' Miliar';
        } elseif (abs($val) >= 1000000) {
            $res = number_format($val / 1000000, 1, ',', '.');
            if (str_ends_with($res, ',0')) {
                $res = substr($res, 0, -2);
            }
            return 'Rp ' . $res . ' Juta';
        } else {
            return 'Rp ' . number_format($val, 0, ',', '.');
        }
    }
}
