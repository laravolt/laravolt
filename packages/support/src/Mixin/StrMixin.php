<?php

declare(strict_types=1);

namespace Laravolt\Support\Mixin;

class StrMixin
{
    public function mask()
    {
        return function ($str, $first, $last, $mask = '*') {
            $len = strlen($str);
            $toShow = $first + $last;

            return substr($str, 0, $len <= $toShow ? 0 : $first) . str_repeat($mask,
                    $len - ($len <= $toShow ? 0 : $toShow)) . substr($str, $len - $last, $len <= $toShow ? 0 : $last);
        };
    }

    public function maskEmail()
    {
        return function ($email) {
            $mails = explode("@", $email);
            $domain = $mails[1] ?? "";

            $mails[0] = mask($mails[0], 3, 2);
            $domain = mask($domain, 3, 2);
            $mails[1] = $domain;

            return implode("@", $mails);
        };
    }
}
