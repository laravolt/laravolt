<?php

declare(strict_types=1);

namespace Laravolt;

use Illuminate\Support\Str;
use InvalidArgumentException;

if (! function_exists('platform_path')) {
    /**
     * Get Laravolt platform absolute directory path.
     */
    function platform_path(string $path): string
    {
        return realpath(__DIR__.'/../'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
}

if (! function_exists('platform_max_file_upload')) {
    /**
     * Determine max file upload size based on php.ini settings.
     *
     * @param  bool  $shorthand
     */
    function platform_max_file_upload($shorthand = false): int|string
    {
        $max_upload = shorthand_to_byte(ini_get('upload_max_filesize'));
        $max_post = shorthand_to_byte(ini_get('post_max_size'));
        $memory_limit = shorthand_to_byte(ini_get('memory_limit'));

        // return the smallest of them, this defines the real limit
        $limit = min($max_upload, $max_post, $memory_limit);

        if ($shorthand) {
            return byte_to_shorthand($limit);
        }

        return $limit;
    }
}

if (! function_exists('shorthand_to_byte')) {
    function shorthand_to_byte($shorthand): int
    {
        $shorthand = mb_trim($shorthand);
        $val = (int) $shorthand;
        $last = mb_strtolower($shorthand[mb_strlen($shorthand) - 1]);

        switch ($last) {
            case 'g':
                $val *= 1073741824;
                break;
            case 'm':
                $val *= 1048576;
                break;
            case 'k':
                $val *= 1024;
                break;
        }

        return $val;
    }
}

if (! function_exists('byte_to_shorthand')) {
    function byte_to_shorthand($size, $precision = 0): string
    {
        static $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size /= $step;
            $i++;
        }

        return round($size, $precision).$units[$i];
    }
}

if (! function_exists('readable_number')) {
    function readable_number(float $value, int $precision = 1): string
    {
        $thresholds = [
            '' => 900,
            'K' => 900000,
            'M' => 900000000,
            'B' => 900000000000,
            'T' => 90000000000000,
        ];

        $default = '900T+';

        foreach ($thresholds as $suffix => $threshold) {
            if ($value < $threshold) {
                $formattedNumber = number_format($value / ($threshold / $thresholds['']), $precision);
                $cleanedNumber = (mb_strpos($formattedNumber, '.') === false)
                    ? $formattedNumber
                    : mb_rtrim(mb_rtrim($formattedNumber, '0'), '.');

                return $cleanedNumber.$suffix;
            }
        }

        return $default;
    }

    if (! function_exists('number_to_rupiah')) {
        function number_to_rupiah(float $number, int $decimals = 2, bool $prefix = true): string
        {
            $symbol = $prefix ? 'Rp' : '';

            return $symbol.number_format($number, $decimals, ',', '.');
        }
    }

    /**
     * @see https://notes.rioastamal.net/2012/03/membuat-fungsi-terbilang-pada-php.html
     */
    if (! function_exists('number_to_terbilang')) {
        function number_to_terbilang($number, string $suffix = 'rupiah'): string
        {
            $numberString = Str::of((string) $number);
            $fraction = $numberString->contains('.') ? $numberString->afterLast('.') : '';
            $angka = (int) $number;

            $bilangan = [
                'no',
                'satu',
                'dua',
                'tiga',
                'empat',
                'lima',
                'enam',
                'tujuh',
                'delapan',
                'sembilan',
                'sepuluh',
                'sebelas',
            ];

            if ($angka === 0) {
                $str = '';
            } elseif ($angka < 12) {
                // mapping angka ke index array $bilangan
                $str = $bilangan[$angka];
            } elseif ($angka < 20) {
                // bilangan 'belasan'
                // misal 18 maka 18 - 10 = 8
                $str = $bilangan[$angka - 10].' belas';
            } elseif ($angka < 100) {
                // bilangan 'puluhan'
                // misal 27 maka 27 / 10 = 2.7 (integer => 2) 'dua'
                // untuk mendapatkan sisa bagi gunakan modulus
                // 27 mod 10 = 7 'tujuh'
                $str = sprintf('%s puluh %s', $bilangan[(int) ($angka / 10)], $bilangan[$angka % 10]);
            } elseif ($angka < 200) {
                // bilangan 'seratusan' (itulah indonesia knp tidak satu ratus saja? :))
                // misal 151 maka 151 = 100 = 51 (hasil berupa 'puluhan')
                // daripada menulis ulang rutin kode puluhan maka gunakan
                // saja fungsi rekursif dengan memanggil fungsi number_to_terbilang(51)
                $str = sprintf(
                    'seratus %s',
                    number_to_terbilang($angka - 100, '')
                );
            } elseif ($angka < 1000) {
                // bilangan 'ratusan'
                // misal 467 maka 467 / 100 = 4,67 (integer => 4) 'empat'
                // sisanya 467 mod 100 = 67 (berupa puluhan jadi gunakan rekursif number_to_terbilang(67))
                $str = sprintf(
                    '%s ratus %s',
                    $bilangan[(int) ($angka / 100)],
                    number_to_terbilang($angka % 100, '')
                );
            } elseif ($angka < 2000) {
                // bilangan 'seribuan'
                // misal 1250 maka 1250 - 1000 = 250 (ratusan)
                // gunakan rekursif number_to_terbilang(250)
                $str = sprintf('seribu %s', number_to_terbilang($angka - 1000, ''));
            } elseif ($angka < 1000000) {
                // bilangan 'ribuan' (sampai ratusan ribu)
                $str = sprintf(
                    '%s ribu %s',
                    number_to_terbilang((int) ($angka / 1000), ''),
                    number_to_terbilang($angka % 1000, '')
                );
            } elseif ($angka < 1000000000) {
                // bilangan 'jutaan' (sampai ratusan juta)
                // 'satu puluh' => SALAH, biasa disebut sepuluh
                // 'satu ratus' => SALAH, biasa disebut seratus
                // 'satu juta' => BENAR, kenapa tidak disebut sejuta? ^_^

                // hasil bagi bisa satuan, belasan, ratusan jadi langsung kita gunakan rekursif
                $str = sprintf(
                    '%s juta %s',
                    number_to_terbilang((int) ($angka / 1000000), ''),
                    number_to_terbilang($angka % 1000000, '')
                );
            } elseif ($angka < 1000000000000) {
                // bilangan 'milyaran'
                // karena batas maksimum integer untuk 32bit sistem adalah 2147483647
                // maka kita gunakan fmod agar dapat menghandle angka yang lebih besar
                $str = sprintf(
                    '%s milyar %s',
                    number_to_terbilang((int) ($angka / 1000000000), ''),
                    number_to_terbilang(fmod($angka, 1000000000), '')
                );
            } elseif ($angka < 1000000000000000000) {
                // bilangan 'triliun'
                $hasil_bagi = $angka / 1000000000000;
                $hasil_mod = fmod($angka, 1000000000000);

                $str = sprintf(
                    '%s triliun %s',
                    number_to_terbilang($hasil_bagi, ''),
                    number_to_terbilang($hasil_mod, '')
                );
            } else {
                throw new InvalidArgumentException('Bilangan terlalu besar');
            }

            $str = mb_trim($str);
            if ($suffix) {
                $str .= ' '.$suffix;
            }

            if ((int) (string) $fraction) {
                $str .= ' koma';
                foreach (mb_str_split($fraction) as $decimal) {
                    $str .= ' '.$bilangan[$decimal];
                }
            }

            return $str;
        }
    }
}
