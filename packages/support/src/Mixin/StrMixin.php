<?php

declare(strict_types=1);

namespace Laravolt\Support\Mixin;

use Closure;
use Illuminate\Support\Str;

class StrMixin
{
    public function humanize()
    {
        return function ($string) {
            return mb_trim(preg_replace('/\s+/', ' ', Str::title(str_replace('_', ' ', $string))));
        };
    }

    public function mask(): Closure
    {
        return static function ($str, $first = 3, $last = 3, $mask = '*') {
            $len = mb_strlen($str);
            $toShow = $first + $last;

            return mb_substr($str, 0, $len <= $toShow ? 0 : $first).str_repeat(
                $mask,
                $len - ($len <= $toShow ? 0 : $toShow)
            ).mb_substr($str, $len - $last, $len <= $toShow ? 0 : $last);
        };
    }

    public function maskEmail(): Closure
    {
        return function ($email) {
            $mails = explode('@', $email);
            $domain = $mails[1] ?? '';

            $mails[0] = Str::mask($mails[0], 2, 2);
            $domain = Str::mask($domain, 2, 5);
            $mails[1] = $domain;

            return implode('@', $mails);
        };
    }
}
