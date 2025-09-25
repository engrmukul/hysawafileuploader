<?php

namespace App\Model\Download;

class Helper
{
    public static function formatIndianNumber($num) {
        $num = (string) $num;
        $last3 = substr($num, -3);
        $rest = substr($num, 0, -3);

        if ($rest != '') {
            $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
            $formatted = $rest . "," . $last3;
        } else {
            $formatted = $last3;
        }

        return $formatted;
    }

    public static function normalizeRtype($v) {
        if (!isset($v) || in_array(strtolower($v), ['', '0', 'empty', 'No'], true)) {
            return '';
        } elseif ($v == 1) {
            return 'Yes';
        }
        return $v;
    }
}
