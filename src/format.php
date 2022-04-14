<?php declare(strict_types = 1);

namespace Psi;

class Format {

    public static function phone (string $phone): string {
        $phone = str_replace(' ', '', $phone);
        if (empty($phone)) return '';
        $pre = '';
        if ($phone[0] == '+') {
            $phone = substr($phone, 1);
            $pre = '+';
        }
        return $pre . implode(' ', str_split($phone, 3));
    }

    // TODO...

}
